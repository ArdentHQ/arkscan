import axios from "axios";
import { FailedExportRequest } from "../includes/helpers";

interface FetchAllParams {
    host: string;
    query: Record<string, unknown>;
    limit?: number;
    transactions?: Record<string, unknown>[];
    timestamp?: number;
}

interface Abortable {
    hasAborted: () => boolean;
}

export class TransactionsApi {
    static async fetch(host: string, query: Record<string, unknown>) {
        const response = await axios.get(`${host}/transactions`, {
            params: query,
        });

        return response.data;
    }

    static async fetchAll(
        { host, query, limit = 100, transactions = [], timestamp }: FetchAllParams,
        instance?: Abortable,
    ): Promise<Record<string, unknown>[]> {
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

            timestamp = (page.data[page.data.length - 1]["timestamp"] as Record<string, number>)["epoch"] - 1;
        } catch (e) {
            throw new FailedExportRequest("There was a problem fetching transactions.", transactions);
        }

        return await this.fetchAll(
            {
                host,
                query,
                limit,
                transactions,
                timestamp,
            },
            instance,
        );
    }
}
