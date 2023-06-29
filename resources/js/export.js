import * as dayjs from "dayjs";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";

import { TransactionsApi } from "./transactions-api";

dayjs.extend(dayjsQuarterOfYear);
dayjs.extend(dayjsLocalizedFormat);

const TransactionsExport = ({ address, userCurrency, rate, network }) => {
    const csvColumns = {
        id: "TXID",
        timestamp: "Timestamp",
        sender: "Sender",
        recipient: "Recipient",
        amount: "Value [:networkCurrency]",
        amountFiat: "Value [:userCurrency]",
        fee: "Fee [:networkCurrency]",
        feeFiat: "Fee [:userCurrency]",
        rate: "Rate [:userCurrency]",
    };

    const columnMapping = {
        timestamp: (transaction) => dayjs(transaction.timestamp.human).format("L LTS"),
        amount: (transaction) => transaction.amount / 1e8,
        fee: (transaction) => transaction.fee / 1e8,
        amountFiat: (transaction) => (transaction.amount / 1e8) * rate,
        feeFiat: (transaction) => (transaction.fee / 1e8) * rate,
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
        requestedSyncAbort: false,
        network,
        userCurrency,
        dateRange: "current_month",
        delimiter: "comma",
        includeHeaderRow: true,
        profile: null,

        hasStartedExport: false,
        dataUri: null,
        hasFinishedExport: false,
        errorMessage: null,
        successMessage: null,

        types: {
            transfers: true,
            votes: true,
            multipayments: true,
            others: true,
        },

        columns: {
            id: true,
            timestamp: true,
            sender: true,
            recipient: true,
            amount: true,
            amountFiat: true,
            fee: true,
            feeFiat: true,
            rate: true,
        },

        exportTransactions() {
            this.hasStartedExport = true;

            this.resetStatus();

            (async () => {
                try {
                    const transactions = await this.fetch({ query: this.requestData() });

                    if (transactions.length === 0) {
                        this.hasFinishedExport = true;

                        return;
                    }

                    this.downloadCsv(transactions);
                } catch (e) {
                    this.errorMessage = "There was a problem fetching transactions.";
                }
            })();
        },

        downloadCsv(transactions) {
            const csvRows = [];
            if (this.includeHeaderRow) {
                csvRows.push(this.getColumns());
            }

            for (const transaction of transactions) {
                const data = [];
                for (const [column, enabled] of Object.entries(this.columns)) {
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

            const csvContent = "data:text/csv;charset=utf-8," + csvRows.map(row => row.join(this.getDelimiter())).join("\n");

            this.successMessage = `A total of ${transactions.length} transactions have been retrieved and are ready for download.`;
            this.hasFinishedExport = true;

            this.dataUri = encodeURI(csvContent);
        },

        requestData() {
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
                type: []
            };

            if (dateFrom) {
                data["timestamp.from"] = this.timeSinceEpoch(dateFrom);
                data["timestamp.to"] = this.timeSinceEpoch(dateTo);
            }

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

            return data;
        },

        timeSinceEpoch(date) {
            const epoch = dayjs(this.network.epoch);

            return date.unix() - epoch.unix();
        },

        getColumns() {
            return Object.entries(this.columns)
                .filter(([column, enabled]) => enabled)
                .map(([column, enabled]) => {
                    return csvColumns[column]
                        .replace(/:userCurrency/, userCurrency)
                        .replace(/:networkCurrency/, network.currency);
                });
        },

        getDelimiter() {
            return delimiterMapping[this.delimiter] || ",";
        },

        canExport() {
            if (Object.values(this.types).filter(enabled => enabled).length === 0) {
                return false;
            }

            return Object.values(this.columns).filter(enabled => enabled).length !== 0;
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

        async fetch({
            query,
            limit = 100,
        }) {
            return TransactionsApi.fetchAll({
                host: network.api,
                limit,
                query,
            });
        },
    };
};

export default TransactionsExport;
