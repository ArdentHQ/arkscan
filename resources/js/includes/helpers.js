import dayjs from "dayjs/esm/index.js";
import dayjsQuarterOfYear from "dayjs/esm/plugin/quarterOfYear/index.js";

dayjs.extend(dayjsQuarterOfYear);

const delimiters = {
    comma: ",",
    semicolon: ";",
    tab: "\t",
    pipe: "|",
};

export const arktoshiToNumber = (value) => value / 1e8;

export const timeSinceEpoch = (date, network) => {
    const epoch = dayjs(network.epoch);

    let duration = date.unix() - epoch.unix();
    if (duration < 0) {
        return 0;
    }

    return duration;
};

export const getDateRange = (dateRange) => {
    let dateFrom = DateFilters[dateRange];
    let dateTo = null;
    if (dateFrom !== null) {
        dateTo = dayjs();
        if (typeof dateFrom.from === "object") {
            dateTo = dateFrom.to;
            dateFrom = dateFrom.from;
        }
    }

    return [dateFrom, dateTo];
};

export const getCustomDateRange = (dateFrom = null, dateTo = null) => {
    dateFrom = dateFrom ? dayjs(dateFrom) : null;
    dateTo = dateTo ? dayjs(dateTo) : null;

    if (dateFrom !== null && dateTo !== null && dateFrom > dateTo) {
        [dateFrom, dateTo] = [dateTo, dateFrom];
    }

    if (dateTo) {
        dateTo = dateTo.add(1, "day").subtract(1, "second");
    }

    return [dateFrom, dateTo];
};

export const formatNumber = (value) => {
    return new Intl.NumberFormat(navigator.language).format(value);
};

export const DateFilters = {
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
    data,
    columns,
    columnTitles,
    columnMapping,
    delimiter,
    includeHeaderRow
) => {
    const csvRows = [];
    if (includeHeaderRow) {
        csvRows.push(columnTitles);
    }

    for (const entry of data) {
        const data = [];
        for (const [column, enabled] of Object.entries(columns)) {
            if (!enabled) {
                continue;
            }

            if (columnMapping[column] !== undefined) {
                data.push(columnMapping[column](entry));
            } else {
                data.push(entry[column]);
            }
        }

        csvRows.push(data);
    }

    return encodeURI(
        "data:text/csv;charset=utf-8," +
            csvRows
                .map((row) => row.join(delimiters[delimiter] || ","))
                .join("\n")
    );
};

export class FailedExportRequest extends Error {
    constructor(message, partialRequestData) {
        super(message);

        this._partialRequestData = partialRequestData;
    }

    get partialRequestData() {
        return this._partialRequestData;
    }
}
