import * as dayjs from "dayjs";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";

import {
    FailedExportRequest,
    arktoshiToNumber,
    formatNumber,
    generateCsv,
    getCustomDateRange,
    getDateRange,
    queryTimestamp,
} from "./includes/helpers";
import {
    ExportStatus,
    TransactionType,
    TransactionTypeGroup,
} from "./includes/enums";

import { TransactionsApi } from "./transactions-api";

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
        if (
            transaction.type === TransactionType.MultiPayment &&
            transaction.typeGroup === TransactionTypeGroup.Core
        ) {
            return transaction.asset.payments.reduce(
                (totalAmount, recipientData) => {
                    if (recipientData.recipientId === address) {
                        if (totalAmount < 0) {
                            totalAmount = 0;
                        }

                        totalAmount += arktoshiToNumber(recipientData.amount);
                    } else if (totalAmount <= 0) {
                        totalAmount -= arktoshiToNumber(recipientData.amount);
                    }

                    return totalAmount;
                },
                0
            );
        }

        if (transaction.sender === address) {
            return -amount;
        }

        return amount;
    };

    const columnMapping = {
        timestamp: (transaction) => dayjs(parseInt(transaction.timestamp)).format("L LTS"),
        recipient: (transaction) => {
            if (transaction.typeGroup !== TransactionTypeGroup.Core) {
                return "Other";
            }

            if (transaction.type === TransactionType.Transfer) {
                return transaction.recipient;
            }

            if (transaction.type === TransactionType.Vote) {
                return "Vote Transaction";
            }

            if (transaction.type === TransactionType.MultiPayment) {
                return `Multiple (${transaction.asset.payments.length})`;
            }

            return "Other";
        },
        amount: getTransactionAmount,
        fee: (transaction) => {
            if (transaction.sender === address) {
                return -arktoshiToNumber(transaction.fee);
            }

            return 0;
        },
        total: (transaction) => {
            const amount = getTransactionAmount(transaction);
            if (transaction.sender === address) {
                return amount - arktoshiToNumber(transaction.fee);
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

                    if (this.types.others) {
                        transactions.push(
                            ...(await this.fetch({
                                query: {
                                    ...this.requestData(true),
                                    typeGroup: TransactionTypeGroup.Magistrate,
                                },
                            }))
                        );
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

        requestData(withoutTransactionTypes = false) {
            const [dateFrom, dateTo] = this.getDateRange();

            const data = {
                address,
                type: [],
            };

            if (dateFrom) {
                data["timestamp.from"] = queryTimestamp(dateFrom);
                data["timestamp.to"] = queryTimestamp(dateTo);
            }

            if (withoutTransactionTypes === false) {
                data.typeGroup = TransactionTypeGroup.Core;

                if (this.types.transfers) {
                    data.type.push(TransactionType.Transfer);
                }

                if (this.types.votes) {
                    data.type.push(TransactionType.Vote);
                }

                if (this.types.multipayments) {
                    data.type.push(TransactionType.MultiPayment);
                }

                if (this.types.others) {
                    data.type.push(
                        TransactionType.SecondSignature,
                        TransactionType.ValidatorRegistration,
                        TransactionType.MultiSignature,
                        TransactionType.Ipfs,
                        TransactionType.ValidatorResignation,
                        TransactionType.HtlcLock,
                        TransactionType.HtlcClaim,
                        TransactionType.HtlcRefund
                    );
                }

                data.type = data.type.join(",");
            }

            return data;
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
                    timestamp:
                        query["timestamp.to"] ??
                        queryTimestamp(dayjs()),
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
