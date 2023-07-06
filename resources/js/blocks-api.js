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

    static async fetchAll(
        {
            host,
            query,
            publicKey,
            limit = 100,
            blocks = [],
            orderBy = "height:desc",
            height,
        },
        instance
    ) {
        const page = await this.request(
            host,
            {
                limit,
                orderBy,
                ...query,
                "height.to": height,
            },
            publicKey
        );

        if (instance?.hasAborted()) {
            return [];
        }

        blocks.push(...page.data);

        height = page.data[page.data.length - 1]["height"] - 1;

        if (page.meta.count < limit) {
            return blocks;
        }

        return await this.fetchAll(
            {
                host,
                query,
                limit,
                blocks,
                publicKey,
                height,
            },
            instance
        );
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
