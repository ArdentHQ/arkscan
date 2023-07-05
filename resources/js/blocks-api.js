import axios from "axios";

export class BlocksApi {
    static async request(host, query, publicKey) {
        const response = await axios.get(
            `${host}/delegates/${publicKey}/blocks`,
            {
                params: query,
            }
        );

        return response.data;
    }

    static async fetchAll({
        cursor = 1,
        host,
        query,
        publicKey,
        limit = 100,
        blocks = [],
        orderBy = "height:desc",
    }, instance) {
        const page = await this.request(
            host,
            {
                page: cursor,
                limit,
                orderBy,
                ...query,
            },
            publicKey
        );

        if (instance?.hasAborted()) {
            return [];
        }

        blocks.push(...page.data);
        cursor = cursor + 1;

        if (page.meta.count < limit) {
            return blocks;
        }

        return await this.fetchAll({
            cursor,
            host,
            query,
            limit,
            blocks,
            publicKey,
        }, instance);
    }

    static async fetch({ host, query, publicKey, orderBy }) {
        const page = await this.request(
            host,
            {
                limit: 1,
                orderBy,
                ...query,
            },
            publicKey
        );

        return page.data.pop();
    }
}
