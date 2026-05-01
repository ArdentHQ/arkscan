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

function buildQuery(query: Record<string, unknown>): string {
    const params = new URLSearchParams();

    for (const [key, value] of Object.entries(query)) {
        if (value === undefined || value === null) {
            continue;
        }

        params.set(key, String(value));
    }

    return params.toString();
}

export class TransactionsApi {
    static async fetch(host: string, query: Record<string, unknown>) {
        const response = await fetch(`${host}/transactions?${buildQuery(query)}`);

        if (!response.ok) {
            throw new Error(`Request failed with status ${response.status}`);
        }

        return response.json();
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
