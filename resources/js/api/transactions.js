import axios from "axios";
import { FailedExportRequest } from "../includes/helpers";

export class TransactionsApi {
    static async fetch(host, query) {
        const response = await axios.get(`${host}/transactions`, {
            params: query,
        });

        return response.data;
    }

    static async fetchAll(
        { host, query, limit = 100, transactions = [], timestamp },
        instance
    ) {
        try {
            const page = await this.fetch(host, {
                limit,
                orderBy: "timestamp:desc,transactionIndex:desc",
                ...query,
                "timestamp.to": timestamp,
            });

            if (instance?.hasAborted()) {
                return [];
            }

            transactions.push(...page.data);

            if (page.meta.count < limit) {
                return transactions;
            }

            timestamp =
                page.data[page.data.length - 1]["timestamp"]["epoch"] - 1;
        } catch (e) {
            throw new FailedExportRequest(
                "There was a problem fetching transactions.",
                transactions
            );
        }

        return await this.fetchAll(
            {
                host,
                query,
                limit,
                transactions,
                timestamp,
            },
            instance
        );
    }
}
