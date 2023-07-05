import axios from "axios";

export class TransactionsApi {
    static async fetch(host, query) {
        const response = await axios.get(`${host}/transactions`, {
            params: query,
        });

        return response.data;
    }

    static async fetchAll({
        host,
        query,
        limit = 100,
        transactions = [],
        timestamp = null,
    }) {
        const page = await this.fetch(host, {
            limit,
            orderBy: "timestamp:desc,sequence:desc",
            ...query,
            "timestamp.to": timestamp,
        });

        transactions.push(...page.data);

        if (page.meta.count < limit) {
            return transactions;
        }

        timestamp = page.data[page.data.length - 1]["timestamp"]["epoch"] - 1;

        return await this.fetchAll({
            host,
            query,
            limit,
            transactions,
            timestamp,
        });
    }
}
