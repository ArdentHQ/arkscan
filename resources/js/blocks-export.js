import dayjs from "dayjs/esm/index.js";
import dayjsLocalizedFormat from "dayjs/esm/plugin/localizedFormat/index.js";

import {
    FailedExportRequest,
    arktoshiToNumber,
    formatNumber,
    generateCsv,
    getCustomDateRange,
    getDateRange,
    queryTimestamp,
} from "./includes/helpers";

import { BlocksApi } from "./api/blocks";
import { ExportStatus } from "./includes/enums";

window.ExportStatus = ExportStatus;

dayjs.extend(dayjsLocalizedFormat);

const csvColumns = {
    id: "Block ID",
    timestamp: "Timestamp",
    numberOfTransactions: "Transactions",
    volume: "Volume [:networkCurrency]",
    volumeFiat: "Volume [:userCurrency]",
    total: "Total Rewards [:networkCurrency]",
    totalFiat: "Total Rewards [:userCurrency]",
    rate: "Rate [:userCurrency]",
};

const BlocksExport = ({
    address,
    userCurrency,
    rates,
    network,
    canBeExchanged,
}) => {
    const columnMapping = {
        timestamp: (block) => dayjs(parseInt(block.timestamp)).format("L LTS"),
        volume: (block) => arktoshiToNumber(block.totalAmount),
        volumeFiat: function (block) {
            return this.volume(block) * this.rate(block);
        },
        total: (block) => {
            return arktoshiToNumber(
                block.totalAmount + block.totalFee + block.reward
            );
        },
        totalFiat: function (block) {
            return this.total(block) * this.rate(block);
        },
        rate: (block) => {
            const date = dayjs(parseInt(block.timestamp)).format("YYYY-MM-DD");

            return rates[date] ?? 0;
        },
    };

    return {
        address,
        network,
        canBeExchanged,
        userCurrency,
        dateRange: "current_month",
        dateFrom: null,
        dateTo: null,
        delimiter: "comma",
        includeHeaderRow: true,

        hasStartedExport: false,
        partialDataUri: null,
        dataUri: null,
        hasFinishedExport: false,
        errorMessage: null,
        successMessage: null,

        columns: {
            id: false,
            timestamp: false,
            numberOfTransactions: false,
            volume: false,
            total: false,
        },

        resetForm() {
            if (canBeExchanged) {
                this.columns.volumeFiat = false;
                this.columns.totalFiat = false;
                this.columns.rate = false;
            }
        },

        exportData() {
            this.hasStartedExport = true;

            this.resetStatus();

            (async () => {
                try {
                    const query = await this.requestData();
                    if (
                        query === {} ||
                        query["height.from"] === 0 ||
                        query["height.to"] === 0
                    ) {
                        this.hasFinishedExport = true;

                        return;
                    }

                    let blocks = await this.fetch({
                        query,
                        address,
                    });

                    if (blocks.length === 0) {
                        this.hasFinishedExport = true;

                        return;
                    }

                    this.downloadCsv(blocks);
                } catch (e) {
                    if (
                        e instanceof FailedExportRequest &&
                        e.partialRequestData.length > 0
                    ) {
                        this.errorMessage = e.message;

                        this.partialDataUri = generateCsv(
                            e.partialRequestData,
                            this.getColumns(),
                            this.getColumnTitles(),
                            columnMapping,
                            this.delimiter,
                            this.includeHeaderRow
                        );
                    } else {
                        this.errorMessage =
                            "There was a problem fetching blocks.";
                    }

                    console.log(this.errorMessage, e);
                }
            })();
        },

        downloadCsv(blocks) {
            this.successMessage = `A total of ${formatNumber(
                blocks.length
            )} blocks have been retrieved and are ready for download.`;
            this.hasFinishedExport = true;

            this.dataUri = generateCsv(
                blocks,
                this.getColumns(),
                this.getColumnTitles(),
                columnMapping,
                this.delimiter,
                this.includeHeaderRow
            );
        },

        getDateRange() {
            if (this.dateRange === "custom") {
                return getCustomDateRange(this.dateFrom, this.dateTo);
            }

            return getDateRange(this.dateRange);
        },

        async requestData() {
            const [dateFrom, dateTo] = this.getDateRange();

            const data = {};

            if (dateFrom) {
                data["timestamp.from"] = queryTimestamp(dateFrom);
                data["timestamp.to"] = queryTimestamp(dateTo);
            }

            return data;
        },

        getColumns() {
            let columns = this.columns;
            if (columns.volume) {
                columns.total = true;
            }

            if (columns.volumeFiat) {
                columns.totalFiat = true;
            }

            const csvColumnsNames = Object.keys(csvColumns);
            columns = Object.entries(columns)
                .sort((a, b) => {
                    return (
                        csvColumnsNames.indexOf(a[0]) -
                        csvColumnsNames.indexOf(b[0])
                    );
                })
                .reduce((enabledColumns, [column, enabled]) => {
                    if (enabled) {
                        enabledColumns[column] = enabled;
                    }

                    return enabledColumns;
                }, {});

            return columns;
        },

        getColumnTitles() {
            return Object.entries(this.getColumns()).map(([column]) => {
                return this.translateColumnCurrency(csvColumns[column]);
            });
        },

        translateColumnCurrency(column) {
            return column
                .replace(/:userCurrency/, userCurrency)
                .replace(/:networkCurrency/, network.currency);
        },

        canExport() {
            if (this.dateRange === "custom") {
                const [dateFrom, dateTo] = getCustomDateRange(
                    this.dateFrom,
                    this.dateTo
                );

                if (dateFrom === null || dateTo === null) {
                    return false;
                }
            }

            return (
                Object.values(this.columns).filter((enabled) => enabled)
                    .length !== 0
            );
        },

        get exportStatus() {
            if (this.hasStartedExport === false) {
                return ExportStatus.PendingExport;
            }

            if (this.errorMessage !== null) {
                return ExportStatus.Error;
            }

            if (this.hasFinishedExport === true && this.dataUri === null) {
                return ExportStatus.Warning;
            }

            if (this.dataUri === null) {
                return ExportStatus.PendingDownload;
            }

            return ExportStatus.Done;
        },

        resetStatus() {
            this.dataUri = null;
            this.partialDataUri = null;
            this.errorMessage = null;
            this.successMessage = null;
            this.hasFinishedExport = false;
        },

        async fetch({ query, address, limit = 100 }) {
            return BlocksApi.fetchAll(
                {
                    host: network.api,
                    limit,
                    query,
                    address,
                    height: query["height.to"],
                },
                this
            );
        },

        hasAborted() {
            if (this.hasStartedExport === false) {
                return true;
            }

            return this.$refs.modal === undefined;
        },
    };
};

export default BlocksExport;
