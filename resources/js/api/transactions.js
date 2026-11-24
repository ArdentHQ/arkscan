import axios from "axios";
import { FailedExportRequest } from "../includes/helpers";

const DAY_IN_SECONDS = 86400;
const FALLBACK_WINDOW_DAYS = 30;
const MAX_WINDOW_DAYS = 360;

const isServerError = (error) => (error?.response?.status ?? 0) >= 500;

export class TransactionsApi {
    static async fetch(host, query) {
        const response = await axios.get(`${host}/transactions`, {
            params: query,
        });

        return response.data;
    }

    // Pages back in time using `timestamp.to` plus an `offset` for the rows
    // already taken at that timestamp, so transactions of the same block are
    // never skipped between pages.
    //
    // Without a lower bound the API has to scan the whole chain to find out a
    // wallet has no older transactions, which times out (500) on the last
    // page of long histories. Requests are bounded by the wallet's oldest
    // transaction, and if one still fails we keep going in time windows.
    static async fetchAll(
        { host, query, limit = 100, timestamp, oldestTimestamp = null },
        instance,
    ) {
        const transactions = [];
        const from = Math.max(
            query["timestamp.from"] ?? 0,
            oldestTimestamp ?? 0,
        );

        let offset = 0;
        let windowDays = null;

        while (timestamp >= from) {
            const windowFrom =
                windowDays === null
                    ? null
                    : Math.max(from, timestamp - windowDays * DAY_IN_SECONDS);

            let page;

            try {
                page = await this.fetch(host, {
                    limit,
                    orderBy: "timestamp:desc,sequence:desc",
                    ...query,
                    "timestamp.to": timestamp,
                    ...(offset > 0 ? { offset } : {}),
                    ...(windowFrom !== null || from > 0
                        ? { "timestamp.from": windowFrom ?? from }
                        : {}),
                });
            } catch (e) {
                if (isServerError(e) && windowDays !== 1) {
                    windowDays =
                        windowDays === null
                            ? FALLBACK_WINDOW_DAYS
                            : Math.max(1, Math.floor(windowDays / 2));

                    continue;
                }

                throw new FailedExportRequest(
                    "There was a problem fetching transactions.",
                    transactions,
                );
            }

            if (instance?.hasAborted()) {
                return [];
            }

            transactions.push(...page.data);

            if (page.data.length === limit) {
                const lastTimestamp =
                    page.data[page.data.length - 1]["timestamp"]["epoch"];
                const takenAtLastTimestamp = page.data.filter(
                    (transaction) =>
                        transaction["timestamp"]["epoch"] === lastTimestamp,
                ).length;

                offset =
                    lastTimestamp === timestamp
                        ? offset + takenAtLastTimestamp
                        : takenAtLastTimestamp;
                timestamp = lastTimestamp;

                continue;
            }

            if (windowFrom === null || windowFrom <= from) {
                break;
            }

            // This window is exhausted, move on to the previous one.
            if (page.data.length === 0) {
                windowDays = Math.min(windowDays * 2, MAX_WINDOW_DAYS);
            }

            timestamp = windowFrom - 1;
            offset = 0;
        }

        return transactions;
    }
}
