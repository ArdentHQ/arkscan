import * as dayjs from "dayjs";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";

dayjs.extend(dayjsQuarterOfYear);
dayjs.extend(dayjsLocalizedFormat);

export class Export {
    dateRange = "current_month";
    delimiter = "comma";
    includeHeaderRow = true;

    hasStartedExport = false;
    dataUri = null;
    hasFinishedExport = false;
    errorMessage = null;
    successMessage = null;

    dateFilters = {
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
    }

    delimiterMapping = {
        comma: ",",
        semicolon: ";",
        tab: "\t",
        pipe: "|",
    }

    constructor(data) {
        for (const [key, value] of Object.entries(data)) {
            this[key] = value;
        }
    }

    resetForm() {
        this.resetStatus();

        for (const column of Object.keys(this.columns)) {
            this.columns[column] = false;
        }
    }

    get exportStatus() {
        if (this.hasStartedExport === false) {
            return "PENDING_EXPORT";
        }

        if (this.errorMessage !== null) {
            return "ERROR";
        }

        if (this.hasFinishedExport === true && this.dataUri === null) {
            return "WARNING";
        }

        if (this.dataUri === null) {
            return "PENDING_DOWNLOAD";
        }

        return "DONE";
    }

    exportData() {
        this.hasStartedExport = true;

        this.resetStatus();

        (async () => {
            try {
                data = await this.performExport();

                if (data.length === 0) {
                    this.hasFinishedExport = true;

                    return;
                }

                this.downloadCsv(data);
            } catch (e) {
                this.errorMessage =
                    "There was a problem exporting.";

                console.log(this.errorMessage, e);
            }
        })();
    }

    generateCsv(entryItems) {
        const csvRows = [];
        if (this.includeHeaderRow) {
            csvRows.push(this.getColumnTitles());
        }

        for (const item of entryItems) {
            const data = [];
            for (const [column, enabled] of Object.entries(
                this.getColumns()
            )) {
                if (!enabled) {
                    continue;
                }

                if (columnMapping[column] !== undefined) {
                    data.push(columnMapping[column](item));
                } else {
                    data.push(item[column]);
                }
            }

            csvRows.push(data);
        }

        this.hasFinishedExport = true;
        this.dataUri = encodeURI(
            "data:text/csv;charset=utf-8," +
                csvRows.map((row) => row.join(this.getDelimiter())).join("\n")
        );
    }

    canExport() {
        return (
            Object.values(this.types).filter((enabled) => enabled)
                .length !== 0
        );
    }

    timeSinceEpoch(date) {
        const epoch = dayjs(this.network.epoch);

        return date.unix() - epoch.unix();
    }

    getColumns() {
        let columns = this.columns;
        if (columns.amount && columns.fee) {
            columns.total = true;
        }

        if (columns.amountFiat && columns.feeFiat) {
            columns.totalFiat = true;
        }

        const csvColumnsNames = Object.keys(csvColumns);
        columns = Object.entries(columns)
            .sort((a, b) => {
                return (
                    csvColumnsNames.indexOf(a[0]) -
                    csvColumnsNames.indexOf(b[0])
                );
            })
            .reduce((enabledColumns, [column, enabled]) => {
                if (enabled) {
                    enabledColumns[column] = enabled;
                }

                return enabledColumns;
            }, {});

        return columns;
    }

    getColumnTitles() {
        return Object.entries(this.getColumns()).map(([column]) => {
            return this.translateColumnCurrency(csvColumns[column]);
        });
    }

    translateColumnCurrency(column) {
        return column
            .replace(/:userCurrency/, userCurrency)
            .replace(/:networkCurrency/, network.currency);
    }

    getDelimiter() {
        return delimiterMapping[this.delimiter] || ",";
    }

    resetStatus() {
        this.dataUri = null;
        this.errorMessage = null;
        this.successMessage = null;
        this.hasFinishedExport = false;
    }

    performExport() {
        throw new Error('[Method not implemented]');
    }

    requestData() {
        throw new Error('[Method not implemented]');
    }
};
