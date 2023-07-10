import * as dayjs from "dayjs";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";

dayjs.extend(dayjsQuarterOfYear);

const delimiters = {
    comma: ",",
    semicolon: ";",
    tab: "\t",
    pipe: "|",
};

export const arktoshiToNumber = (value) => value / 1e8;

export const getDelimiter = (delimiter) => {
    return delimiters[delimiter] || ",";
};

export const timeSinceEpoch = (date, network) => {
    const epoch = dayjs(network.epoch);

    return date.unix() - epoch.unix();
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

export const formatNumber = (value) => {
    return new Intl.NumberFormat(navigator.language).format(value);
}

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
