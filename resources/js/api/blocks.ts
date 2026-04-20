import { FailedExportRequest } from "../includes/helpers";
import axios from "axios";

interface FetchAllParams {
    host: string;
    query: Record<string, unknown>;
    address: string;
    limit?: number;
    blocks?: Record<string, unknown>[];
    orderBy?: string;
    height?: number;
}

interface Abortable {
    hasAborted: () => boolean;
}

export class BlocksApi {
    static async request(host: string, query: Record<string, unknown>, address: string) {
        const response = await axios.get(`${host}/validators/${address}/blocks`, {
            params: query,
        });

        return response.data;
    }

    static async fetchAll(
        { host, query, address, limit = 100, blocks = [], orderBy = "number:desc", height }: FetchAllParams,
        instance?: Abortable,
    ): Promise<Record<string, unknown>[]> {
        try {
            const page = await this.request(
                host,
                {
                    limit,
                    orderBy,
                    ...query,
                    "number.to": height,
                },
                address,
            );

            if (instance?.hasAborted()) {
                return [];
            }

            blocks.push(...page.data);

            if (page.meta.count < limit) {
                return blocks;
            }

            height = page.data[page.data.length - 1]["number"] - 1;
        } catch (e) {
            throw new FailedExportRequest("There was a problem fetching blocks.", blocks);
        }

        return await this.fetchAll(
            {
                host,
                query,
                limit,
                blocks,
                address,
                height,
            },
            instance,
        );
    }

    static async fetch({
        host,
        query,
        address,
        orderBy,
    }: {
        host: string;
        query: Record<string, unknown>;
        address: string;
        orderBy: string;
    }) {
        const page = await this.request(
            host,
            {
                limit: 1,
                orderBy,
                ...query,
            },
            address,
        );

        return page.data.pop();
    }
}
