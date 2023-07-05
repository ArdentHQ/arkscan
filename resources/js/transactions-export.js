import * as dayjs from "dayjs";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";
import { arktoshiToNumber, getDelimiter, DateFilters, timeSinceEpoch } from "./includes/helpers";
import { TransactionsApi } from "./transactions-api";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";

import {
    ExportStatus,
    TransactionType,
    TransactionTypeGroup,
} from "./includes/enums";

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
        timestamp: (transaction) =>
            dayjs(transaction.timestamp.human).format("L LTS"),
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
            const date = dayjs(transaction.timestamp.human).format(
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
        delimiter: "comma",
        includeHeaderRow: true,

        hasStartedExport: false,
        dataUri: null,
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

        exportTransactions() {
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
                    this.errorMessage =
                        "There was a problem fetching transactions.";

                    console.log(this.errorMessage, e);
                }
            })();
        },

        downloadCsv(transactions) {
            const csvRows = [];
            if (this.includeHeaderRow) {
                csvRows.push(this.getColumnTitles());
            }

            for (const transaction of transactions) {
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
                csvRows.map((row) => row.join(getDelimiter(this.delimiter))).join("\n");

            this.successMessage = `A total of ${transactions.length} transactions have been retrieved and are ready for download.`;
            this.hasFinishedExport = true;

            this.dataUri = encodeURI(csvContent);
        },

        requestData(withoutTransactionTypes = false) {
            let dateFrom = DateFilters[this.dateRange];
            let dateTo = null;
            if (dateFrom !== null) {
                dateTo = dayjs();
                if (typeof dateFrom.from === "object") {
                    dateTo = dateFrom.to;
                    dateFrom = dateFrom.from;
                }
            }

            const data = {
                address,
                type: [],
            };

            if (dateFrom) {
                data["timestamp.from"] = timeSinceEpoch(dateFrom, this.network);
                data["timestamp.to"] = timeSinceEpoch(dateTo, this.network);
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
                        TransactionType.DelegateRegistration,
                        TransactionType.MultiSignature,
                        TransactionType.Ipfs,
                        TransactionType.DelegateResignation,
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
                        query["timestamp.to"] ?? timeSinceEpoch(dayjs(), this.network),
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
