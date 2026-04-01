import dayjs, { type Dayjs } from "dayjs/esm/index.js";
import dayjsQuarterOfYear from "dayjs/esm/plugin/quarterOfYear/index.js";
import Decimal from "decimal.js";

dayjs.extend(dayjsQuarterOfYear);

const delimiters: Record<string, string> = {
    comma: ",",
    semicolon: ";",
    tab: "\t",
    pipe: "|",
};

export const arktoshiToNumber = (value: number): number => value / 1e18;

export const queryTimestamp = (date: Dayjs): number => {
    return date.unix() * 1000;
};

interface DateRange {
    from: Dayjs;
    to: Dayjs;
}

export const getDateRange = (dateRange: string): [Dayjs | null, Dayjs | null] => {
    let dateFrom: Dayjs | DateRange | null = DateFilters[dateRange as keyof typeof DateFilters];
    let dateTo: Dayjs | null = null;
    if (dateFrom !== null) {
        dateTo = dayjs();
        if (typeof dateFrom === "object" && "from" in dateFrom) {
            dateTo = dateFrom.to;
            dateFrom = dateFrom.from;
        }
    }

    return [dateFrom as Dayjs | null, dateTo];
};

export const getCustomDateRange = (
    dateFrom: string | null = null,
    dateTo: string | null = null,
): [Dayjs | null, Dayjs | null] => {
    let from: Dayjs | null = dateFrom ? dayjs(dateFrom) : null;
    let to: Dayjs | null = dateTo ? dayjs(dateTo) : null;

    if (from !== null && to !== null && from > to) {
        [from, to] = [to, from];
    }

    if (to) {
        to = to.add(1, "day").subtract(1, "second");
    }

    return [from, to];
};

export const formatNumber = (value: number): string => {
    return new Intl.NumberFormat(navigator.language).format(value);
};

export const DateFilters: Record<string, Dayjs | DateRange | null> = {
    current_month: dayjs().startOf("month"),
    last_month: {
        from: dayjs().subtract(1, "month").startOf("month"),
        to: dayjs().subtract(1, "month").endOf("month"),
    },
    last_quarter: {
        from: dayjs().subtract(1, "quarter").startOf("quarter"),
        to: dayjs().subtract(1, "quarter").endOf("quarter"),
    },
    current_year: dayjs().startOf("year"),
    last_year: {
        from: dayjs().subtract(1, "year").startOf("year"),
        to: dayjs().subtract(1, "year").endOf("year"),
    },
    all: null,
};

export const generateCsv = (
    data: Record<string, unknown>[],
    columns: Record<string, boolean>,
    columnTitles: string[],
    columnMapping: Record<string, (entry: Record<string, unknown>) => unknown>,
    delimiter: string,
    includeHeaderRow: boolean,
): string => {
    const formatCsvNumber = (value: unknown): unknown => {
        if (typeof value === "number") {
            return new Decimal(value).toFixed();
        }

        return value;
    };

    const csvRows: unknown[][] = [];
    if (includeHeaderRow) {
        csvRows.push(columnTitles);
    }

    for (const entry of data) {
        const dataRow: unknown[] = [];

        for (const [column, enabled] of Object.entries(columns)) {
            if (!enabled) {
                continue;
            }

            let value: unknown;
            if (columnMapping[column] !== undefined) {
                value = columnMapping[column](entry);
            } else {
                value = entry[column];
            }

            dataRow.push(formatCsvNumber(value));
        }

        csvRows.push(dataRow);
    }

    return encodeURI(
        "data:text/csv;charset=utf-8," + csvRows.map((row) => row.join(delimiters[delimiter] || ",")).join("\n"),
    );
};

export class FailedExportRequest extends Error {
    private _partialRequestData: unknown[];

    constructor(message: string, partialRequestData: unknown[]) {
        super(message);

        this._partialRequestData = partialRequestData;
    }

    get partialRequestData(): unknown[] {
        return this._partialRequestData;
    }
}
