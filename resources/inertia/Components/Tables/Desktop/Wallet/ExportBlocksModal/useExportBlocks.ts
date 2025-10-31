import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import dayjs from "dayjs";
import localizedFormat from "dayjs/plugin/localizedFormat";
import { useTranslation } from "react-i18next";
import { BlocksApi } from "@js/api/blocks";
import { ExportStatus } from "@js/includes/enums";
import {
    FailedExportRequest,
    arktoshiToNumber,
    formatNumber,
    generateCsv,
    getCustomDateRange,
    getDateRange,
    queryTimestamp,
} from "@js/includes/helpers";

dayjs.extend(localizedFormat);

interface UseExportBlocksProps {
    isOpen: boolean;
    address: string;
    network: any;
    userCurrency: string;
    rates: Record<string, number>;
    canBeExchanged: boolean;
}

interface UseExportBlocksReturn {
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
    selectedColumns: string[];
    setSelectedColumns: (values: string[]) => void;
    hasStartedExport: boolean;
    setHasStartedExport: (value: boolean) => void;
    dataUri: string | null;
    partialDataUri: string | null;
    hasFinishedExport: boolean;
    exportStatus: string;
    errorMessage: string | null;
    successMessage: string | null;
    exportedCount: number;
    canExport: () => boolean;
    resetForm: () => void;
    resetStatus: () => void;
    exportData: () => Promise<void>;
}

type FailedExportError = Error & {
    partialRequestData?: any[];
};

export default function useExportBlocks({
    isOpen,
    address,
    network,
    userCurrency,
    rates,
    canBeExchanged,
}: UseExportBlocksProps): UseExportBlocksReturn {
    const { t } = useTranslation();

    const csvColumnOrder = useMemo(() => {
        const base: string[] = ["id", "timestamp", "numberOfTransactions", "volume", "total"];

        if (canBeExchanged) {
            base.push("volumeFiat", "totalFiat", "rate");
        }

        return base;
    }, [canBeExchanged]);

    const [dateRange, setDateRange] = useState<string>("current_month");
    const [dateFrom, setDateFrom] = useState<Date | null>(null);
    const [dateTo, setDateTo] = useState<Date | null>(null);
    const [delimiter, setDelimiter] = useState<string>("comma");
    const [includeHeaderRow, setIncludeHeaderRow] = useState<boolean>(true);
    const [selectedColumns, setSelectedColumns] = useState<string[]>([]);

    const [hasStartedExport, setHasStartedExport] = useState(false);
    const [dataUri, setDataUri] = useState<string | null>(null);
    const [partialDataUri, setPartialDataUri] = useState<string | null>(null);
    const [hasFinishedExport, setHasFinishedExport] = useState(false);
    const [errorMessage, setErrorMessage] = useState<string | null>(null);
    const [successMessage, setSuccessMessage] = useState<string | null>(null);
    const [exportedCount, setExportedCount] = useState(0);

    const abortControllerRef = useRef<AbortController | null>(null);

    const columnMapping = useMemo(() => {
        return {
            id: (block: any) => block?.hash ?? "",
            timestamp: (block: any) => {
                const numericTimestamp = Number.parseInt(block?.timestamp ?? 0, 10);

                return dayjs(numericTimestamp).format("L LTS");
            },
            numberOfTransactions: (block: any) => block?.transactionsCount ?? block?.transactionCount ?? 0,
            volume: (block: any) => arktoshiToNumber(Number(block?.amount ?? 0)),
            volumeFiat: function (block: any) {
                return this.volume(block) * this.rate(block);
            },
            total: (block: any) => {
                const amount = Number(block?.amount ?? 0);
                const fee = Number(block?.fee ?? 0);
                const reward = Number(block?.reward ?? 0);

                return arktoshiToNumber(amount + fee + reward);
            },
            totalFiat: function (block: any) {
                return this.total(block) * this.rate(block);
            },
            rate: (block: any) => {
                const numericTimestamp = Number.parseInt(block?.timestamp ?? 0, 10);
                const dateKey = dayjs(numericTimestamp).format("YYYY-MM-DD");

                return rates?.[dateKey] ?? 0;
            },
        };
    }, [rates]);

    const getColumnLabel = useCallback(
        (columnKey: string) => {
            const translationKey = `pages.wallet.export-blocks-modal.columns-options.${columnKey}`;

            const fallbackMap: Record<string, string> = {
                volume: `Volume [${network?.currency ?? ""}]`,
                volumeFiat: `Volume [${userCurrency ?? ""}]`,
                total: `Total Rewards [${network?.currency ?? ""}]`,
                totalFiat: `Total Rewards [${userCurrency ?? ""}]`,
                rate: `Rate [${userCurrency ?? ""}]`,
                numberOfTransactions: t("tables.blocks.transactions"),
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
            const [customFrom, customTo] = getCustomDateRange(dateFrom, dateTo);

            if (!customFrom || !customTo) {
                return false;
            }
        }

        return selectedColumns.length > 0;
    }, [dateFrom, dateRange, dateTo, selectedColumns.length]);

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
        setSelectedColumns([]);
        setDateFrom(null);
        setDateTo(null);
        resetStatus();
    }, [resetStatus]);

    const buildQuery = useCallback(() => {
        const query: Record<string, any> = {};

        if (dateRange === "custom") {
            const [customFrom, customTo] = getCustomDateRange(dateFrom, dateTo);

            if (customFrom && customTo) {
                query["timestamp.from"] = queryTimestamp(customFrom);
                query["timestamp.to"] = queryTimestamp(customTo);
            }
        } else if (dateRange !== "all") {
            const [rangeFrom, rangeTo] = getDateRange(dateRange);

            if (rangeFrom && rangeTo) {
                query["timestamp.from"] = queryTimestamp(rangeFrom);
                query["timestamp.to"] = queryTimestamp(rangeTo);
            }
        }

        return query;
    }, [dateFrom, dateRange, dateTo]);

    const generateCsvFromBlocks = useCallback(
        (blocks: any[]) => {
            const selectedSet = new Set(selectedColumns);
            const columnsForCsv: Record<string, boolean> = {};

            csvColumnOrder.forEach((columnKey) => {
                columnsForCsv[columnKey] = selectedSet.has(columnKey);
            });

            if (columnsForCsv.volume) {
                columnsForCsv.total = true;
            }

            if (columnsForCsv.volumeFiat) {
                columnsForCsv.totalFiat = true;
            }

            if (!canBeExchanged) {
                ["volumeFiat", "totalFiat", "rate"].forEach((columnKey) => {
                    columnsForCsv[columnKey] = false;
                });
            }

            const columnTitles = csvColumnOrder
                .filter((columnKey) => columnsForCsv[columnKey])
                .map((columnKey) => getColumnLabel(columnKey));

            return generateCsv(blocks, columnsForCsv, columnTitles, columnMapping, delimiter, includeHeaderRow);
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

            const blocks = await BlocksApi.fetchAll(
                {
                    host: network?.api || "",
                    query,
                    address,
                    limit: 100,
                    height: query["height.to"],
                },
                {
                    hasAborted: () => controller.signal.aborted,
                },
            );

            if (controller.signal.aborted) {
                return;
            }

            setExportedCount(blocks.length);

            if (blocks.length === 0) {
                setHasFinishedExport(true);
                return;
            }

            const csvUri = generateCsvFromBlocks(blocks);

            setDataUri(csvUri);
            setSuccessMessage(
                `A total of ${formatNumber(blocks.length)} blocks have been retrieved and are ready for download.`,
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
                    setPartialDataUri(generateCsvFromBlocks(partialData));
                    setErrorMessage(failedError.message);
                } else {
                    setErrorMessage("There was a problem fetching blocks.");
                }
            } else {
                setErrorMessage("There was a problem fetching blocks.");
            }
        }
    }, [address, buildQuery, generateCsvFromBlocks, network?.api, resetStatus]);

    useEffect(() => {
        if (isOpen) {
            resetForm();
        }
    }, [isOpen, resetForm]);

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
        selectedColumns,
        setSelectedColumns,
        hasStartedExport,
        setHasStartedExport,
        dataUri,
        partialDataUri,
        hasFinishedExport,
        exportStatus,
        errorMessage,
        successMessage,
        exportedCount,
        canExport,
        resetForm,
        resetStatus,
        exportData,
    };
}
