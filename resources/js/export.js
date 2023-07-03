import * as dayjs from "dayjs";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";

import { TransactionsApi } from "./transactions-api";

dayjs.extend(dayjsQuarterOfYear);
dayjs.extend(dayjsLocalizedFormat);

const TransactionsExport = ({ address, userCurrency, rate, network, canBeExchanged }) => {
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
        let amount = transaction.amount / 1e8;
        if (transaction.type === 6 && transaction.typeGroup === 1) {
            return transaction.asset.payments.reduce(
                (totalAmount, recipientData) => {
                    if (recipientData.recipientId === address) {
                        if (totalAmount < 0) {
                            totalAmount = 0;
                        }

                        totalAmount += recipientData.amount / 1e8;
                    } else if (totalAmount <= 0) {
                        totalAmount -= recipientData.amount / 1e8;
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
            if (transaction.typeGroup === 2) {
                return "Other";
            }

            if (transaction.type === 0) {
                return transaction.recipient;
            }

            if (transaction.type === 3) {
                return "Vote Transaction";
            }

            if (transaction.type === 6) {
                return `Multiple (${transaction.asset.payments.length})`;
            }

            return "Other";
        },
        amount: getTransactionAmount,
        fee: (transaction) => {
            if (transaction.sender === address) {
                return -transaction.fee / 1e8;
            }

            return 0;
        },
        total: (transaction) => {
            const amount = getTransactionAmount(transaction);
            if (transaction.sender === address) {
                return amount - (transaction.fee / 1e8);
            }

            return amount;
        },
        amountFiat: function (transaction) {
            return this.amount(transaction) * rate
        },
        feeFiat: function (transaction) {
            return this.fee(transaction) * rate
        },
        totalFiat: function (transaction) {
            return this.total(transaction) * rate
        },
        rate: () => rate,
    };

    const delimiterMapping = {
        comma: ",",
        semicolon: ";",
        tab: "\t",
        pipe: "|",
    };

    const dateFilters = {
        current_month: dayjs().startOf("month"),
        last_month: {
            from: dayjs().subtract(1, "month").startOf("month"),
            to: dayjs().subtract(1, "month").endOf("month"),
        },
        last_quarter: {
            from: dayjs().subtract(1, "quarter").startOf("quarter"),
            to: dayjs().subtract(1, "quarter").endOf("quarter"),
        },
        current_year: dayjs().startOf("year"),
        last_year: {
            from: dayjs().subtract(1, "year").startOf("year"),
            to: dayjs().subtract(1, "year").endOf("year"),
        },
        all: null,
    };

    return {
        address,
        transactions: [],
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

            this.transactions = [];
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

                    if (this.types.others) {
                        transactions.push(
                            ...(await this.fetch({
                                query: {
                                    ...this.requestData(true),
                                    typeGroup: 2,
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
                for (const [column, enabled] of Object.entries(this.getColumns())) {
                    if (! enabled) {
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
                csvRows.map((row) => row.join(this.getDelimiter())).join("\n");

            this.successMessage = `A total of ${transactions.length} transactions have been retrieved and are ready for download.`;
            this.hasFinishedExport = true;

            this.dataUri = encodeURI(csvContent);
        },

        requestData(withoutTransactionTypes = false) {
            let dateFrom = dateFilters[this.dateRange];
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
                data["timestamp.from"] = this.timeSinceEpoch(dateFrom);
                data["timestamp.to"] = this.timeSinceEpoch(dateTo);
            }

            if (withoutTransactionTypes === false) {
                data.typeGroup = 1;

                if (this.types.transfers) {
                    data.type.push(0);
                }

                if (this.types.votes) {
                    data.type.push(3);
                }

                if (this.types.multipayments) {
                    data.type.push(6);
                }

                if (this.types.others) {
                    data.type.push(1, 2, 4, 5, 7, 8, 9, 10);
                }

                data.type = data.type.join(",");
            }

            return data;
        },

        timeSinceEpoch(date) {
            const epoch = dayjs(this.network.epoch);

            return date.unix() - epoch.unix();
        },

        getColumns() {
            let columns = this.columns;
            if (columns.amount && columns.fee) {
                columns.total = true;
            }

            if (columns.amountFiat && columns.feeFiat) {
                columns.totalFiat = true;
            }

            const csvColumnsNames = Object.keys(csvColumns);
            columns = Object.entries(columns)
                .sort((a, b) => {
                    return csvColumnsNames.indexOf(a[0]) - csvColumnsNames.indexOf(b[0]);
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
            return Object.entries(this.getColumns())
                .map(([column]) => {
                    return this.translateColumnCurrency(csvColumns[column]);
                });
        },

        translateColumnCurrency(column) {
            return column
                .replace(/:userCurrency/, userCurrency)
                .replace(/:networkCurrency/, network.currency)
        },

        getDelimiter() {
            return delimiterMapping[this.delimiter] || ",";
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
                return "PENDING_EXPORT";
            }

            if (this.errorMessage !== null) {
                return "ERROR";
            }

            if (this.hasFinishedExport === true && this.dataUri === null) {
                return "WARNING";
            }

            if (this.dataUri === null) {
                return "PENDING_DOWNLOAD";
            }

            return "DONE";
        },

        resetStatus() {
            this.dataUri = null;
            this.errorMessage = null;
            this.successMessage = null;
            this.hasFinishedExport = false;
        },

        async fetch({ query, limit = 100 }) {
            return TransactionsApi.fetchAll({
                host: network.api,
                limit,
                query,
            });
        },
    };
};

export default TransactionsExport;
