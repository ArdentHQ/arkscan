import { useState, useEffect, useMemo, useCallback, useRef } from "react";
import { useTranslation } from "react-i18next";
// @ts-ignore
import { TransactionsApi } from "@js/api/transactions";
// @ts-ignore
import { ExportStatus } from "@js/includes/enums";
// @ts-ignore
import {
    arktoshiToNumber,
    queryTimestamp,
    getDateRange,
    getCustomDateRange,
    formatNumber,
    DateFilters,
    generateCsv,
    FailedExportRequest,
} from "@js/includes/helpers";

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
    exportStatus: string | null;
    errorMessage: string | null;
    successMessage: string | null;
    exportedCount: number;

    // Methods
    canExport: () => boolean;
    resetForm: () => void;
    resetStatus: () => void;
    exportData: () => Promise<void>;
}

export default function useExportTransactions({
    isOpen,
    address,
    network,
    userCurrency,
    rates,
    canBeExchanged,
}: UseExportTransactionsProps): UseExportTransactionsReturn {
    const { t } = useTranslation();

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
    const [exportStatus, setExportStatus] = useState<string | null>(null);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [exportedCount, setExportedCount] = useState(0);

    // Abort controller for canceling ongoing requests
    const abortControllerRef = useRef<AbortController | null>(null);

    // Column definitions
    const columns = useMemo(
        () => ({
            id: {
                label: t("pages.wallet.export-transactions-modal.columns-options.id"),
                mapValue: (transaction: any) => transaction.id,
            },
            timestamp: {
                label: t("pages.wallet.export-transactions-modal.columns-options.timestamp"),
                mapValue: (transaction: any) => transaction.timestamp.human,
            },
            sender: {
                label: t("pages.wallet.export-transactions-modal.columns-options.sender"),
                mapValue: (transaction: any) => transaction.sender,
            },
            recipient: {
                label: t("pages.wallet.export-transactions-modal.columns-options.recipient"),
                mapValue: (transaction: any) => transaction.recipient,
            },
            amount_arktoshi: {
                label: t("pages.wallet.export-transactions-modal.columns-options.amount_arktoshi"),
                mapValue: (transaction: any) => transaction.amount,
            },
            amount: {
                label: t("pages.wallet.export-transactions-modal.columns-options.amount", {
                    networkCurrency: network?.currency || "",
                }),
                mapValue: (transaction: any) => arktoshiToNumber(transaction.amount),
            },
            amount_fiat: {
                label: t("pages.wallet.export-transactions-modal.columns-options.amount_fiat", {
                    userCurrency: userCurrency || "",
                }),
                mapValue: (transaction: any) => {
                    const rate = rates[userCurrency];
                    if (!rate) return 0;
                    return formatNumber(arktoshiToNumber(transaction.amount) * rate);
                },
                criteria: canBeExchanged,
            },
            fee_arktoshi: {
                label: t("pages.wallet.export-transactions-modal.columns-options.fee_arktoshi"),
                mapValue: (transaction: any) => transaction.fee,
            },
            fee: {
                label: t("pages.wallet.export-transactions-modal.columns-options.fee", {
                    networkCurrency: network?.currency || "",
                }),
                mapValue: (transaction: any) => arktoshiToNumber(transaction.fee),
            },
            fee_fiat: {
                label: t("pages.wallet.export-transactions-modal.columns-options.fee_fiat", {
                    userCurrency: userCurrency || "",
                }),
                mapValue: (transaction: any) => {
                    const rate = rates[userCurrency];
                    if (!rate) return 0;
                    return formatNumber(arktoshiToNumber(transaction.fee) * rate);
                },
                criteria: canBeExchanged,
            },
            type: {
                label: t("pages.wallet.export-transactions-modal.columns-options.type"),
                mapValue: (transaction: any) => transaction.typeGroup,
            },
        }),
        [t, network, userCurrency, rates, canBeExchanged],
    );

    // Check if can export
    const canExport = useCallback(() => {
        if (dateRange === "custom") {
            if (!dateFrom || !dateTo) {
                return false;
            }
        }

        if (selectedTypes.length === 0) {
            return false;
        }

        return selectedColumns.length > 0;
    }, [dateRange, dateFrom, dateTo, selectedTypes, selectedColumns]);

    // Reset status
    const resetStatus = useCallback(() => {
        setDataUri(null);
        setPartialDataUri(null);
        setErrorMessage(null);
        setSuccessMessage(null);
        setHasFinishedExport(false);
        setExportStatus(null);
        setExportedCount(0);
    }, []);

    // Reset form
    const resetForm = useCallback(() => {
        setHasStartedExport(false);
        setIncludeHeaderRow(true);
        setDateRange("current_month");
        setDelimiter("comma");
        setSelectedTypes([]);
        setSelectedColumns([]);
        resetStatus();
    }, [resetStatus]);

    // Build the query for fetching transactions
    const buildQuery = useCallback(() => {
        const query: any = {};

        if (dateRange === "custom") {
            if (dateFrom && dateTo) {
                const [customDateFrom, customDateTo] = getCustomDateRange(dateFrom, dateTo);
                if (customDateFrom && customDateTo) {
                    query["timestamp.from"] = queryTimestamp(customDateFrom);
                    query["timestamp.to"] = queryTimestamp(customDateTo);
                }
            }
        } else if (dateRange !== "all") {
            const [rangeFrom, rangeTo] = getDateRange(dateRange as DateFilters);
            if (rangeFrom && rangeTo) {
                query["timestamp.from"] = queryTimestamp(rangeFrom);
                query["timestamp.to"] = queryTimestamp(rangeTo);
            }
        }

        query.address = address;

        const dataValues: string[] = [];

        selectedTypes.forEach((type) => {
            if (type === "transfers") {
                dataValues.push("0x");
            } else if (type === "votes") {
                if (network?.contractMethods?.vote) {
                    dataValues.push(network.contractMethods.vote);
                }
                if (network?.contractMethods?.unvote) {
                    dataValues.push(network.contractMethods.unvote);
                }
            } else if (type === "multipayments") {
                if (network?.contractMethods?.multipayment) {
                    dataValues.push(network.contractMethods.multipayment);
                }
            } else if (type === "others") {
                if (network?.contractMethods?.validator_registration) {
                    dataValues.push(network.contractMethods.validator_registration);
                }
                if (network?.contractMethods?.validator_resignation) {
                    dataValues.push(network.contractMethods.validator_resignation);
                }
                if (network?.contractMethods?.validator_update) {
                    dataValues.push(network.contractMethods.validator_update);
                }
                if (network?.contractMethods?.username_registration) {
                    dataValues.push(network.contractMethods.username_registration);
                }
                if (network?.contractMethods?.username_resignation) {
                    dataValues.push(network.contractMethods.username_resignation);
                }
            }
        });

        if (dataValues.length > 0) {
            query.data = dataValues.join(",");
        }

        return query;
    }, [dateRange, dateFrom, dateTo, selectedTypes, address, network]);

    // Generate CSV from transactions
    const generateCsvFromTransactions = useCallback(
        (transactions: any[]) => {
            const selectedColumnsObj: Record<string, boolean> = {};
            selectedColumns.forEach((col) => {
                selectedColumnsObj[col] = true;
            });

            const columnTitles = selectedColumns.map((col) => {
                const column = columns[col as keyof typeof columns];
                return column ? column.label : "";
            });

            const columnMapping: Record<string, (transaction: any) => any> = {};
            selectedColumns.forEach((col) => {
                const column = columns[col as keyof typeof columns];
                if (column) {
                    columnMapping[col] = column.mapValue;
                }
            });

            return generateCsv(
                transactions,
                selectedColumnsObj,
                columnTitles,
                columnMapping,
                delimiter,
                includeHeaderRow,
            );
        },
        [selectedColumns, columns, delimiter, includeHeaderRow],
    );

    // Export data
    const exportData = useCallback(async () => {
        setHasStartedExport(true);
        resetStatus();
        setExportStatus(ExportStatus.PendingExport);

        // Create abort controller
        abortControllerRef.current = new AbortController();

        try {
            const query = buildQuery();

            // Get the initial timestamp for pagination
            // If we have a timestamp.to in the query, use that, otherwise use current time
            const timestamp = query["timestamp.to"] || Date.now();

            // Remove timestamp.to from query because fetchAll will set it
            if (query["timestamp.to"]) {
                delete query["timestamp.to"];
            }

            // Fetch all transactions
            const transactions = await TransactionsApi.fetchAll(
                {
                    host: network?.api || "",
                    query,
                    limit: 100,
                    transactions: [],
                    timestamp,
                },
                {
                    hasAborted: () => abortControllerRef.current?.signal.aborted || false,
                },
            );

            // Check if aborted
            if (abortControllerRef.current?.signal.aborted) {
                setExportStatus(null);
                return;
            }

            setExportedCount(transactions.length);

            // Generate CSV
            const csv = generateCsvFromTransactions(transactions);
            const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
            const url = URL.createObjectURL(blob);

            setDataUri(url);
            setExportStatus(ExportStatus.PendingDownload);
            setSuccessMessage(t("pages.wallet.export-transactions-modal.success", { count: transactions.length }));
            setHasFinishedExport(true);
        } catch (error: unknown) {
            console.error("Export error:", error);

            if (error instanceof FailedExportRequest) {
                // Partial export
                if ((error as any).transactions && (error as any).transactions.length > 0) {
                    setExportedCount((error as any).transactions.length);
                    const csv = generateCsvFromTransactions((error as any).transactions);
                    const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
                    const url = URL.createObjectURL(blob);

                    setPartialDataUri(url);
                    setExportStatus(ExportStatus.Warning);
                    setErrorMessage(
                        t("pages.wallet.export-transactions-modal.partial_error", {
                            count: (error as any).transactions.length,
                        }),
                    );
                } else {
                    setExportStatus(ExportStatus.Error);
                    setErrorMessage(t("pages.wallet.export-transactions-modal.error"));
                }
            } else {
                setExportStatus(ExportStatus.Error);
                setErrorMessage(t("pages.wallet.export-transactions-modal.error"));
            }
            setHasFinishedExport(true);
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
