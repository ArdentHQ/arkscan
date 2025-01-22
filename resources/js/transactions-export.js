import dayjs from "dayjs/esm/index.js";
import dayjsLocalizedFormat from "dayjs/esm/plugin/localizedFormat/index.js";
import dayjsQuarterOfYear from "dayjs/esm/plugin/quarterOfYear/index.js";

import {
    FailedExportRequest,
    arktoshiToNumber,
    formatNumber,
    generateCsv,
    getCustomDateRange,
    getDateRange,
    queryTimestamp,
} from "./includes/helpers";
import { ExportStatus } from "./includes/enums";

import { TransactionsApi } from "./api/transactions";

window.ExportStatus = ExportStatus;

dayjs.extend(dayjsQuarterOfYear);
dayjs.extend(dayjsLocalizedFormat);

const TransactionsExport = ({
    address,
    userCurrency,
    rates,
    network,
    canBeExchanged,
}) => {
    const csvColumns = {
        id: "TXID",
        timestamp: "Timestamp",
        sender: "Sender",
        recipient: "Recipient",
        amount: "Value [:networkCurrency]",
        fee: "Fee [:networkCurrency]",
        total: "Total [:networkCurrency]",
        amountFiat: "Value [:userCurrency]",
        feeFiat: "Fee [:userCurrency]",
        totalFiat: "Total [:userCurrency]",
        rate: "Rate [:userCurrency]",
    };

    const getTransactionAmount = (transaction) => {
        let amount = arktoshiToNumber(transaction.amount);
        if (transaction.senderAddress === address) {
            return -amount;
        }

        return amount;
    };

    const columnMapping = {
        timestamp: (transaction) =>
            dayjs(parseInt(transaction.timestamp)).format("L LTS"),
        recipient: (transaction) => {
            return transaction.recipientAddress;
        },
        sender: (transaction) => {
            return transaction.senderAddress;
        },
        amount: getTransactionAmount,
        fee: (transaction) => {
            if (transaction.senderAddress === address) {
                return -arktoshiToNumber(transaction.gasPrice);
            }

            return 0;
        },
        total: (transaction) => {
            const amount = getTransactionAmount(transaction);
            if (transaction.senderAddress === address) {
                return amount - arktoshiToNumber(transaction.gasPrice);
            }

            return amount;
        },
        amountFiat: function (transaction) {
            return this.amount(transaction) * this.rate(transaction);
        },
        feeFiat: function (transaction) {
            return this.fee(transaction) * this.rate(transaction);
        },
        totalFiat: function (transaction) {
            return this.total(transaction) * this.rate(transaction);
        },
        rate: (transaction) => {
            const date = dayjs(parseInt(transaction.timestamp)).format(
                "YYYY-MM-DD"
            );

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
        dataUri: null,
        partialDataUri: null,
        hasFinishedExport: false,
        errorMessage: null,
        successMessage: null,

        types: {
            transfers: false,
            votes: false,
            multipayments: false,
            others: false,
        },

        columns: {
            id: false,
            timestamp: false,
            sender: false,
            recipient: false,
            amount: false,
            fee: false,
        },

        resetForm() {
            this.resetStatus();

            this.includeHeaderRow = true;
            this.dateRange = "current_month";
            this.delimiter = "comma";

            for (const type of Object.keys(this.types)) {
                this.types[type] = false;
            }

            for (const column of Object.keys(this.columns)) {
                this.columns[column] = false;
            }

            if (canBeExchanged) {
                this.columns.amountFiat = false;
                this.columns.feeFiat = false;
                this.columns.rate = false;
            }
        },

        exportData() {
            this.hasStartedExport = true;

            this.resetStatus();

            (async () => {
                try {
                    const transactions = await this.fetch({
                        query: this.requestData(),
                    });

                    if (this.hasAborted()) {
                        return;
                    }

                    if (transactions.length === 0) {
                        this.hasFinishedExport = true;

                        return;
                    }

                    this.downloadCsv(transactions);
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
                            "There was a problem fetching transactions.";
                    }

                    console.log(this.errorMessage, e);
                }
            })();
        },

        downloadCsv(transactions) {
            this.successMessage = `A total of ${formatNumber(
                transactions.length
            )} transactions have been retrieved and are ready for download.`;
            this.hasFinishedExport = true;

            this.dataUri = generateCsv(
                transactions,
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

        requestData() {
            const [dateFrom, dateTo] = this.getDateRange();

            const requestData = {
                address,
                data: [],
            };

            if (dateFrom) {
                requestData["timestamp.from"] = queryTimestamp(dateFrom);
                requestData["timestamp.to"] = queryTimestamp(dateTo);
            }

            if (this.types.transfers) {
                requestData.data.push("0x");
            }

            if (this.types.votes) {
                requestData.data.push(
                    network.contract_methods.vote,
                    network.contract_methods.unvote
                );
            }

            if (this.types.multipayments) {
                requestData.data.push(network.contract_methods.multipayment);
            }

            if (this.types.others) {
                requestData.data.push(
                    network.contract_methods.validator_registration,
                    network.contract_methods.validator_resignation,
                    network.contract_methods.username_registration,
                    network.contract_methods.username_resignation
                );
            }

            requestData.data = requestData.data.join(",");

            return requestData;
        },

        getColumns() {
            let columns = this.columns;

            columns.total = columns.amount && columns.fee;
            columns.totalFiat = columns.amountFiat && columns.feeFiat;

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

            if (
                Object.values(this.types).filter((enabled) => enabled)
                    .length === 0
            ) {
                return false;
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

        async fetch({ query, limit = 100 }) {
            return TransactionsApi.fetchAll(
                {
                    host: network.api,
                    limit,
                    query,
                    timestamp: query["timestamp.to"] ?? queryTimestamp(dayjs()),
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

export default TransactionsExport;
