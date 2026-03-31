import dayjs, { Dayjs } from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import localizedFormat from "dayjs/plugin/localizedFormat";
import { DATE_FORMAT, DATE_TIME_FORMAT } from "@/constants";

dayjs.extend(dayjsRelativeTime);
dayjs.extend(localizedFormat);

export function formatDate(unixSeconds: number): string {
    return dayjs(unixSeconds * 1000).format(DATE_FORMAT);
}

export function formatDateTime(unixSeconds: number): string {
    return dayjs(unixSeconds * 1000).format(DATE_TIME_FORMAT);
}

export function formatDayjsDateTime(date: Dayjs): string {
    return date.format(DATE_TIME_FORMAT);
}

export function formatAge(unixSeconds: number): { relative: string; tooltip: string } {
    const date = dayjs(unixSeconds * 1000);

    return {
        relative: dayjs().to(date),
        tooltip: date.format(DATE_TIME_FORMAT),
    };
}

export function formatLocalizedDateTime(timestampMs: number): string {
    return dayjs(timestampMs).format("L LTS");
}

export function formatDateKey(timestampMs: number): string {
    return dayjs(timestampMs).format("YYYY-MM-DD");
}

export function formatUnixDateTime(unixSeconds: number): string {
    return dayjs.unix(unixSeconds).format(DATE_TIME_FORMAT);
}

export function parseTimestamp(transaction: any): Dayjs {
    const { timestamp } = transaction || {};

    if (timestamp === undefined || timestamp === null) {
        return dayjs(0);
    }

    if (typeof timestamp === "object") {
        if (typeof timestamp.unix === "number") {
            return dayjs.unix(timestamp.unix);
        }

        if (typeof timestamp.epoch === "number") {
            return dayjs.unix(timestamp.epoch);
        }

        if (typeof timestamp.human === "string") {
            return dayjs(timestamp.human);
        }
    }

    const numericTimestamp = Number(timestamp);

    if (!Number.isNaN(numericTimestamp)) {
        if (`${timestamp}`.length > 10) {
            return dayjs(numericTimestamp);
        }

        return dayjs.unix(numericTimestamp);
    }

    try {
        return dayjs(timestamp);
    } catch (error) {
        return dayjs(0);
    }
}
