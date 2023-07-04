import * as dayjs from "dayjs";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";

import { Export } from "./export";
import { TransactionsApi } from "./transactions-api";

dayjs.extend(dayjsQuarterOfYear);
dayjs.extend(dayjsLocalizedFormat);

class TransactionsExport extends Export {
    static initialize({
        address,
        userCurrency,
        rate,
        network,
        canBeExchanged,
    }) {
        return new this({
            address,
            userCurrency,
            rate,
            network,
            canBeExchanged,
        });
    }

    types = {
        transfers: false,
        votes: false,
        multipayments: false,
        others: false,
    }

    columns = {
        id: false,
        timestamp: false,
        sender: false,
        recipient: false,
        amount: false,
        fee: false,
    }

    csvColumns = {
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
    }

    columnMapping = {
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
        amount: this.getTransactionAmount,
        fee: (transaction) => {
            if (transaction.sender === address) {
                return -this.arktoshiToFiat(transaction.fee);
            }

            return 0;
        },
        total: (transaction) => {
            const amount = this.getTransactionAmount(transaction);
            if (transaction.sender === address) {
                return amount - this.arktoshiToFiat(transaction.fee);
            }

            return amount;
        },
        amountFiat: function (transaction) {
            return this.amount(transaction) * rate;
        },
        feeFiat: function (transaction) {
            return this.fee(transaction) * rate;
        },
        totalFiat: function (transaction) {
            return this.total(transaction) * rate;
        },
        rate: () => rate,
    }

    arktoshiToFiat = (value) => value / 1e8;

    getTransactionAmount(transaction) {
        let amount = this.arktoshiToFiat(transaction.amount);
        if (transaction.type === 6 && transaction.typeGroup === 1) {
            return transaction.asset.payments.reduce(
                (totalAmount, recipientData) => {
                    if (recipientData.recipientId === address) {
                        if (totalAmount < 0) {
                            totalAmount = 0;
                        }

                        totalAmount += this.arktoshiToFiat(recipientData.amount);
                    } else if (totalAmount <= 0) {
                        totalAmount -= this.arktoshiToFiat(recipientData.amount);
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
    }

    resetForm() {
        super.resetForm();

        this.includeHeaderRow = true;
        this.dateRange = "current_month";
        this.delimiter = "comma";

        for (const type of Object.keys(this.types)) {
            this.types[type] = false;
        }

        if (this.canBeExchanged) {
            this.columns.amountFiat = false;
            this.columns.feeFiat = false;
            this.columns.rate = false;
        }
    }

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
    }

    canExport() {
        if (
            Object.values(this.types).filter((enabled) => enabled)
                .length === 0
        ) {
            return false;
        }

        return super.canExport();
    }

    async performExport() {
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

        return transactions;
    }

    async fetch({ query, limit = 100 }) {
        return TransactionsApi.fetchAll({
            host: network.api,
            limit,
            query,
        });
    }

    generateCsv(transactions) {
        super.generateCsv(transactions);

        this.successMessage = `A total of ${transactions.length} transactions have been retrieved and are ready for download.`;
    }
};

export default TransactionsExport;
