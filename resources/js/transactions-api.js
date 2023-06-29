import axios from "axios";

export class TransactionsApi {
    static async fetch(host, query) {
        const response = await axios.get(`${host}/transactions`, {
            params: query,
        });

        return response.data;
    }

    static async fetchAll({
        cursor = 1,
        host,
        query,
        limit = 100,
        transactions = [],
    }) {
        const page = await this.fetch(host, {
            page: cursor,
            limit,
            orderBy: "timestamp:desc",
            ...query,
        });

        transactions.push(...page.data);
        cursor = cursor + 1;

        // Last page.
        // TODO: Not relying on totalCount because it is an estimate
        //       and is not giving accurate pagination info. Address this issue after initial implementation.
        if (page.meta.count < limit) {
            return transactions;
        }

        return await this.fetchAll({
            cursor,
            host,
            query,
            limit,
            transactions,
        });
    }
}
