import * as dayjs from "dayjs";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";

import {
    arktoshiToNumber,
    getDateRange,
    getDelimiter,
    timeSinceEpoch,
} from "./includes/helpers";

import { BlocksApi } from "./blocks-api";
import { ExportStatus } from "./includes/enums";

window.ExportStatus = ExportStatus;

dayjs.extend(dayjsLocalizedFormat);

const csvColumns = {
    id: "Block ID",
    timestamp: "Timestamp",
    transactions: "Transactions",
    volume: "Volume [:networkCurrency]",
    volumeFiat: "Volume [:userCurrency]",
    total: "Total Rewards [:networkCurrency]",
    totalFiat: "Total Rewards [:userCurrency]",
    rate: "Rate [:userCurrency]",
};

const BlocksExport = ({
    publicKey,
    userCurrency,
    rates,
    network,
    canBeExchanged,
}) => {
    const columnMapping = {
        timestamp: (block) => dayjs(block.timestamp.human).format("L LTS"),
        volume: (block) => arktoshiToNumber(block.forged.amount),
        volumeFiat: function (block) {
            return this.volume(block) * this.rate(block);
        },
        total: (block) => arktoshiToNumber(block.forged.total),
        totalFiat: function (block) {
            return this.total(block) * this.rate(block);
        },
        rate: (block) => {
            const date = dayjs(block.timestamp.human).format("YYYY-MM-DD");

            return rates[date] ?? 0;
        },
    };

    return {
        publicKey,
        network,
        canBeExchanged,
        userCurrency,
        dateRange: "current_month",
        dateFrom: null,
        dateTo: null,
        delimiter: "comma",
        includeHeaderRow: true,

        hasStartedExport: false,
        dataUri: null,
        hasFinishedExport: false,
        errorMessage: null,
        successMessage: null,

        columns: {
            id: false,
            timestamp: false,
            transactions: false,
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
                        query["height.from"] === 0 ||
                        query["height.to"] === 0
                    ) {
                        this.hasFinishedExport = true;

                        return;
                    }

                    let blocks = await this.fetch({
                        query,
                        publicKey,
                    });

                    if (blocks.length === 0) {
                        this.hasFinishedExport = true;

                        return;
                    }

                    this.downloadCsv(blocks);
                } catch (e) {
                    this.errorMessage = "There was a problem fetching blocks.";

                    console.log(this.errorMessage, e);
                }
            })();
        },

        downloadCsv(blocks) {
            const csvRows = [];
            if (this.includeHeaderRow) {
                csvRows.push(this.getColumnTitles());
            }

            for (const transaction of blocks) {
                const data = [];
                for (const [column, enabled] of Object.entries(
                    this.getColumns()
                )) {
                    if (!enabled) {
                        continue;
                    }

                    if (columnMapping[column] !== undefined) {
                        data.push(columnMapping[column](transaction));
                    } else {
                        data.push(transaction[column]);
                    }
                }

                csvRows.push(data);
            }

            const csvContent =
                "data:text/csv;charset=utf-8," +
                csvRows
                    .map((row) => row.join(getDelimiter(this.delimiter)))
                    .join("\n");

            this.successMessage = `A total of ${blocks.length} blocks have been retrieved and are ready for download.`;
            this.hasFinishedExport = true;

            this.dataUri = encodeURI(csvContent);
        },

        getDateRange() {
            if (this.dateRange === "custom") {
                return this.getCustomDateRange();
            }

            return getDateRange[this.dateRange];
        },

        getCustomDateRange() {
            let dateFrom = this.dateFrom ? dayjs(this.dateFrom) : null;
            let dateTo = this.dateTo ? dayjs(this.dateTo) : null;

            if (dateFrom !== null && dateTo !== null && dateFrom > dateTo) {
                [dateFrom, dateTo] = [dateTo, dateFrom];
            }

            return [dateFrom, dateTo];
        },

        async requestData() {
            const [dateFrom, dateTo] = this.getDateRange();

            const data = {};

            if (dateFrom) {
                data["height.from"] = await this.getFirstBlockHeightAfterEpoch(
                    timeSinceEpoch(dateFrom, this.network)
                );
                data["height.to"] = await this.getFirstBlockHeightBeforeEpoch(
                    timeSinceEpoch(dateTo, this.network)
                );
            }

            return data;
        },

        async getFirstBlockHeightAfterEpoch(epoch) {
            return this.getBlockHeight({
                query: {
                    "timestamp.from": epoch,
                },
                orderBy: "timestamp:asc",
            });
        },

        // The API options "timestamp:desc" & "timestamp.to" can cause 500 errors.
        // We do it this way and attempt to get the first block after (the epoch - 1 round) instead.
        async getFirstBlockHeightBeforeEpoch(epoch) {
            return await this.getBlockHeight({
                query: {
                    "timestamp.from":
                        epoch -
                        this.network.blockTime * this.network.delegateCount,
                },
                orderBy: "timestamp:asc",
            });
        },

        async getBlockHeight({ query, orderBy }) {
            const block = await BlocksApi.fetch({
                host: network.api,
                query,
                orderBy,
                publicKey,
            });

            return block?.height ?? 0;
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
                const [dateFrom, dateTo] = this.getCustomDateRange();

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
            this.errorMessage = null;
            this.successMessage = null;
            this.hasFinishedExport = false;
        },

        async fetch({ query, publicKey, limit = 100 }) {
            return BlocksApi.fetchAll(
                {
                    host: network.api,
                    limit,
                    query,
                    publicKey,
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
