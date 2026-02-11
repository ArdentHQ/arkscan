import { useState, useEffect, useMemo, useCallback, useRef } from "react";
import dayjs from "dayjs";
import localizedFormat from "dayjs/plugin/localizedFormat";
import { useTranslation } from "react-i18next";
import { TransactionsApi } from "@js/api/transactions";
import { ExportStatus } from "@js/includes/enums";
import {
    arktoshiToNumber,
    queryTimestamp,
    getDateRange,
    getCustomDateRange,
    formatNumber,
    generateCsv,
    FailedExportRequest,
} from "@js/includes/helpers";

dayjs.extend(localizedFormat);

interface UseExportTransactionsProps {
    isOpen: boolean;
    address: string;
    network: any;
    userCurrency: string;
    rates: Record<string, number>;
    canBeExchanged: boolean;
}

interface UseExportTransactionsReturn {
    // Form state
    dateRange: string;
    setDateRange: (value: string) => void;
    dateFrom: Date | null;
    setDateFrom: (value: Date | null) => void;
    dateTo: Date | null;
    setDateTo: (value: Date | null) => void;
    delimiter: string;
    setDelimiter: (value: string) => void;
    includeHeaderRow: boolean;
    setIncludeHeaderRow: (value: boolean) => void;
    selectedTypes: string[];
    setSelectedTypes: (values: string[]) => void;
    selectedColumns: string[];
    setSelectedColumns: (values: string[]) => void;

    // Export state
    hasStartedExport: boolean;
    setHasStartedExport: (value: boolean) => void;
    dataUri: string | null;
    partialDataUri: string | null;
    hasFinishedExport: boolean;
    exportStatus: string;
    errorMessage: string | null;
    successMessage: string | null;
    exportedCount: number;

    // Methods
    canExport: () => boolean;
    resetForm: () => void;
    resetStatus: () => void;
    exportData: () => Promise<void>;
}

type FailedExportError = Error & {
    partialRequestData?: any[];
};

export default function useExportTransactions({
    isOpen,
    address,
    network,
    userCurrency,
    rates,
    canBeExchanged,
}: UseExportTransactionsProps): UseExportTransactionsReturn {
    const { t } = useTranslation();

    const normalizedAddress = useMemo(() => address?.toLowerCase() ?? "", [address]);

    const csvColumnOrder = useMemo(() => {
        const base: string[] = ["id", "timestamp", "sender", "recipient", "amount", "fee", "total"];

        if (canBeExchanged) {
            base.push("amountFiat", "feeFiat", "totalFiat", "rate");
        }

        return base as string[];
    }, [canBeExchanged]);

    // Form state
    const [dateRange, setDateRange] = useState<string>("current_month");
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);
    const [delimiter, setDelimiter] = useState<string>("comma");
    const [includeHeaderRow, setIncludeHeaderRow] = useState<boolean>(true);
    const [selectedTypes, setSelectedTypes] = useState<string[]>([]);
    const [selectedColumns, setSelectedColumns] = useState<string[]>([]);

    // Export state
    const [hasStartedExport, setHasStartedExport] = useState(false);
    const [dataUri, setDataUri] = useState<string | null>(null);
    const [partialDataUri, setPartialDataUri] = useState<string | null>(null);
    const [hasFinishedExport, setHasFinishedExport] = useState(false);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [exportedCount, setExportedCount] = useState(0);

    const abortControllerRef = useRef<AbortController | null>(null);

    const parseTimestamp = useCallback((transaction: any) => {
        const { timestamp } = transaction || {};

        if (timestamp === undefined || timestamp === null) {
            return dayjs(0);
        }

        if (typeof timestamp === "object") {
            if (typeof timestamp.unix === "number") {
                return dayjs.unix(timestamp.unix);
            }

            if (typeof timestamp.epoch === "number") {
                return dayjs.unix(timestamp.epoch);
            }

            if (typeof timestamp.human === "string") {
                return dayjs(timestamp.human);
            }
        }

        const numericTimestamp = Number(timestamp);

        if (!Number.isNaN(numericTimestamp)) {
            if (`${timestamp}`.length > 10) {
                return dayjs(numericTimestamp);
            }

            return dayjs.unix(numericTimestamp);
        }

        try {
            return dayjs(timestamp);
        } catch (error) {
            return dayjs(0);
        }
    }, []);

    const resolveNumericField = useCallback((transaction: any, keys: string[]) => {
        for (const key of keys) {
            const value = transaction?.[key];

            if (value === undefined || value === null) {
                continue;
            }

            if (typeof value === "number") {
                return value;
            }

            if (typeof value === "string") {
                const trimmed = value.trim();

                if (trimmed.length === 0) {
                    continue;
                }

                if (trimmed.startsWith("0x")) {
                    const parsed = Number.parseInt(trimmed, 16);

                    if (!Number.isNaN(parsed)) {
                        return parsed;
                    }
                }

                const numeric = Number(trimmed);

                if (!Number.isNaN(numeric)) {
                    return numeric;
                }
            }
        }

        return 0;
    }, []);

    const getTransactionAmount = useCallback(
        (transaction: any) => {
            const amountRaw = resolveNumericField(transaction, ["value", "amount", "amount_arktoshi"]);
            const amount = arktoshiToNumber(amountRaw ?? 0);

            if (!transaction?.from || normalizedAddress.length === 0) {
                return amount;
            }

            return transaction.from.toLowerCase() === normalizedAddress ? -amount : amount;
        },
        [normalizedAddress, resolveNumericField],
    );

    const getTransactionFee = useCallback(
        (transaction: any) => {
            const feeRaw = resolveNumericField(transaction, ["gasPrice", "gas_price", "fee"]);
            const fee = arktoshiToNumber(feeRaw ?? 0);

            if (!transaction?.from || normalizedAddress.length === 0) {
                return fee;
            }

            return transaction.from.toLowerCase() === normalizedAddress ? -fee : 0;
        },
        [normalizedAddress, resolveNumericField],
    );

    const getTransactionTotal = useCallback(
        (transaction: any) => {
            const amount = getTransactionAmount(transaction);
            const feeRaw = resolveNumericField(transaction, ["gasPrice", "gas_price", "fee"]);
            const fee = arktoshiToNumber(feeRaw ?? 0);

            if (!transaction?.from || normalizedAddress.length === 0) {
                return amount;
            }

            if (transaction.from.toLowerCase() !== normalizedAddress) {
                return amount;
            }

            return amount - fee;
        },
        [getTransactionAmount, normalizedAddress, resolveNumericField],
    );

    const getTransactionRate = useCallback(
        (transaction: any) => {
            const dateKey = parseTimestamp(transaction).format("YYYY-MM-DD");

            return rates?.[dateKey] ?? 0;
        },
        [parseTimestamp, rates],
    );

    const columnMapping = useMemo(() => {
        return {
            id: (transaction: any) => transaction?.hash ?? transaction?.id ?? "",
            timestamp: (transaction: any) => parseTimestamp(transaction).format("L LTS"),
            sender: (transaction: any) => transaction?.from ?? transaction?.sender ?? "",
            recipient: (transaction: any) => transaction?.to ?? transaction?.recipient ?? "",
            amount: (transaction: any) => getTransactionAmount(transaction),
            fee: (transaction: any) => getTransactionFee(transaction),
            total: (transaction: any) => getTransactionTotal(transaction),
            amountFiat: (transaction: any) => getTransactionAmount(transaction) * getTransactionRate(transaction),
            feeFiat: (transaction: any) => getTransactionFee(transaction) * getTransactionRate(transaction),
            totalFiat: (transaction: any) => getTransactionTotal(transaction) * getTransactionRate(transaction),
            rate: (transaction: any) => getTransactionRate(transaction),
        };
    }, [getTransactionAmount, getTransactionFee, getTransactionRate, getTransactionTotal, parseTimestamp]);

    const getColumnLabel = useCallback(
        (columnKey: string) => {
            const translationKey = `pages.wallet.export-transactions-modal.columns-options.${columnKey}`;

            const fallbackMap: Record<string, string> = {
                amount: `Value [${network?.currency ?? ""}]`,
                amountFiat: `Value [${userCurrency ?? ""}]`,
                fee: `Fee [${network?.currency ?? ""}]`,
                feeFiat: `Fee [${userCurrency ?? ""}]`,
                total: `Total [${network?.currency ?? ""}]`,
                totalFiat: `Total [${userCurrency ?? ""}]`,
                rate: `Rate [${userCurrency ?? ""}]`,
            };

            const translated = t(translationKey, {
                networkCurrency: network?.currency ?? "",
                userCurrency: userCurrency ?? "",
            });

            if (translated === translationKey && fallbackMap[columnKey]) {
                return fallbackMap[columnKey];
            }

            return translated;
        },
        [network?.currency, t, userCurrency],
    );

    const canExport = useCallback(() => {
        if (dateRange === "custom") {
            const [customDateFrom, customDateTo] = getCustomDateRange(dateFrom, dateTo);

            if (!customDateFrom || !customDateTo) {
                return false;
            }
        }

        if (selectedTypes.length === 0) {
            return false;
        }

        return selectedColumns.length > 0;
    }, [dateRange, dateFrom, dateTo, selectedTypes, selectedColumns]);

    const resetStatus = useCallback(() => {
        setDataUri(null);
        setPartialDataUri(null);
        setErrorMessage(null);
        setSuccessMessage(null);
        setHasFinishedExport(false);
        setExportedCount(0);
    }, []);

    const resetForm = useCallback(() => {
        setHasStartedExport(false);
        setIncludeHeaderRow(true);
        setDateRange("current_month");
        setDelimiter("comma");
        setSelectedTypes([]);
        setSelectedColumns([]);
        resetStatus();
    }, [resetStatus]);

    const buildQuery = useCallback(() => {
        const query: Record<string, any> = {
            address,
        };

        if (dateRange === "custom") {
            const [customDateFrom, customDateTo] = getCustomDateRange(dateFrom, dateTo);

            if (customDateFrom && customDateTo) {
                query["timestamp.from"] = queryTimestamp(customDateFrom);
                query["timestamp.to"] = queryTimestamp(customDateTo);
            }
        } else if (dateRange !== "all") {
            const [rangeFrom, rangeTo] = getDateRange(dateRange);

            if (rangeFrom && rangeTo) {
                query["timestamp.from"] = queryTimestamp(rangeFrom);
                query["timestamp.to"] = queryTimestamp(rangeTo);
            }
        }

        const dataFilters: string[] = [];
        const contractMethods = network?.contractMethods ?? network?.contract_methods ?? {};

        selectedTypes.forEach((type) => {
            if (type === "transfers") {
                dataFilters.push("0x");
            }

            if (type === "votes") {
                if (contractMethods?.vote) {
                    dataFilters.push(contractMethods.vote);
                }

                if (contractMethods?.unvote) {
                    dataFilters.push(contractMethods.unvote);
                }
            }

            if (type === "multipayments") {
                if (contractMethods?.multipayment) {
                    dataFilters.push(contractMethods.multipayment);
                }
            }

            if (type === "others") {
                [
                    "validator_registration",
                    "validator_resignation",
                    "validator_update",
                    "username_registration",
                    "username_resignation",
                    "contract_deployment",
                ].forEach((key) => {
                    if (contractMethods?.[key]) {
                        dataFilters.push(contractMethods[key]);
                    }
                });
            }
        });

        if (dataFilters.length > 0) {
            query.data = dataFilters.join(",");
        }

        return query;
    }, [address, dateFrom, dateRange, dateTo, network?.contractMethods, network?.contract_methods, selectedTypes]);

    const generateCsvFromTransactions = useCallback(
        (transactions: any[]) => {
            const selectedSet = new Set(selectedColumns);
            const columnsForCsv: Record<string, boolean> = {};

            csvColumnOrder.forEach((columnKey) => {
                if (columnKey === "total") {
                    columnsForCsv[columnKey] = selectedSet.has("amount") && selectedSet.has("fee");
                    return;
                }

                if (columnKey === "totalFiat") {
                    columnsForCsv[columnKey] =
                        canBeExchanged && selectedSet.has("amountFiat") && selectedSet.has("feeFiat");
                    return;
                }

                if (["amountFiat", "feeFiat", "rate"].includes(columnKey)) {
                    columnsForCsv[columnKey] = canBeExchanged && selectedSet.has(columnKey);
                    return;
                }

                columnsForCsv[columnKey] = selectedSet.has(columnKey);
            });

            const columnTitles = csvColumnOrder
                .filter((columnKey) => columnsForCsv[columnKey])
                .map((columnKey) => getColumnLabel(columnKey));

            return generateCsv(transactions, columnsForCsv, columnTitles, columnMapping, delimiter, includeHeaderRow);
        },
        [selectedColumns, csvColumnOrder, canBeExchanged, getColumnLabel, columnMapping, delimiter, includeHeaderRow],
    );

    const exportData = useCallback(async () => {
        if (abortControllerRef.current) {
            abortControllerRef.current.abort();
        }

        const controller = new AbortController();
        abortControllerRef.current = controller;

        setHasStartedExport(true);
        resetStatus();

        try {
            const query = buildQuery();
            const timestamp = query["timestamp.to"] ?? queryTimestamp(dayjs());

            const transactions = await TransactionsApi.fetchAll(
                {
                    host: network?.api || "",
                    query,
                    limit: 100,
                    transactions: [],
                    timestamp,
                },
                {
                    hasAborted: () => controller.signal.aborted,
                },
            );

            if (controller.signal.aborted) {
                return;
            }

            setExportedCount(transactions.length);

            if (transactions.length === 0) {
                setHasFinishedExport(true);
                return;
            }

            const csvUri = generateCsvFromTransactions(transactions);

            setDataUri(csvUri);
            setSuccessMessage(
                String(
                    t("pages.wallet.export-transactions-modal.success_message", "", {
                        count: formatNumber(transactions.length) as unknown as number,
                    }),
                ),
            );
            setHasFinishedExport(true);
        } catch (error: unknown) {
            console.error("Export error:", error);

            if (controller.signal.aborted) {
                return;
            }

            if (error instanceof FailedExportRequest) {
                const failedError = error as FailedExportError;
                const partialData = failedError.partialRequestData ?? [];

                if (partialData.length > 0) {
                    setExportedCount(partialData.length);
                    setPartialDataUri(generateCsvFromTransactions(partialData));
                    setErrorMessage(failedError.message);
                } else {
                    setErrorMessage(t("pages.wallet.export-transactions-modal.error"));
                }
            } else {
                setErrorMessage(t("pages.wallet.export-transactions-modal.error"));
            }
        }
    }, [buildQuery, generateCsvFromTransactions, network, resetStatus, t]);

    // Reset form when modal opens
    useEffect(() => {
        if (isOpen) {
            resetForm();
        }
    }, [isOpen, resetForm]);

    // Cleanup on unmount
    useEffect(() => {
        return () => {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }
        };
    }, []);

    const exportStatus = useMemo(() => {
        if (!hasStartedExport) {
            return ExportStatus.PendingExport;
        }

        if (errorMessage) {
            return ExportStatus.Error;
        }

        if (hasFinishedExport && !dataUri) {
            return ExportStatus.Warning;
        }

        if (!dataUri) {
            return ExportStatus.PendingDownload;
        }

        return ExportStatus.Done;
    }, [dataUri, errorMessage, hasFinishedExport, hasStartedExport]);

    return {
        // Form state
        dateRange,
        setDateRange,
        dateFrom,
        setDateFrom,
        dateTo,
        setDateTo,
        delimiter,
        setDelimiter,
        includeHeaderRow,
        setIncludeHeaderRow,
        selectedTypes,
        setSelectedTypes,
        selectedColumns,
        setSelectedColumns,

        // Export state
        hasStartedExport,
        setHasStartedExport,
        dataUri,
        partialDataUri,
        hasFinishedExport,
        exportStatus,
        errorMessage,
        successMessage,
        exportedCount,

        // Methods
        canExport,
        resetForm,
        resetStatus,
        exportData,
    };
}
