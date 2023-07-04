import Alpine from "alpinejs";
import * as dayjs from "dayjs";
import * as dayjsQuarterOfYear from "dayjs/plugin/quarterOfYear";
import * as dayjsLocalizedFormat from "dayjs/plugin/localizedFormat";

dayjs.extend(dayjsQuarterOfYear);
dayjs.extend(dayjsLocalizedFormat);

export class Export {
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

    constructor(data, $store) {
        Alpine.store(`exports:${this.constructor.name}`, {
            ...data,

            dateRange: "current_month",
            delimiter: "comma",
            includeHeaderRow: true,

            hasStartedExport: false,
            dataUri: null,
            hasFinishedExport: false,
            errorMessage: null,
            successMessage: null,
        });

        this.$export = $store[`exports:${this.constructor.name}`];
    }

    resetForm() {
        this.resetStatus();

        for (const column of Object.keys(this.$export.columns)) {
            this.$export.columns[column] = false;
        }
    }

    get exportStatus() {
        if (this.$export.hasStartedExport === false) {
            return "PENDING_EXPORT";
        }

        if (this.$export.errorMessage !== null) {
            return "ERROR";
        }

        if (this.$export.hasFinishedExport === true && this.$export.dataUri === null) {
            return "WARNING";
        }

        if (this.$export.dataUri === null) {
            return "PENDING_DOWNLOAD";
        }

        return "DONE";
    }

    exportData = () => {
        this.$export.hasStartedExport = true;

        this.resetStatus();

        (async () => {
            try {
                const data = await this.performExport();

                if (data.length === 0) {
                    this.$export.hasFinishedExport = true;

                    return;
                }

                this.generateCsv(data);
            } catch (e) {
                this.errorMessage =
                    "There was a problem exporting.";

                console.log(this.errorMessage, e);
            }
        })();
    }

    generateCsv(entryItems) {
        const csvRows = [];
        if (this.$export.includeHeaderRow) {
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

                if (this.columnMapping[column] !== undefined) {
                    data.push(this.columnMapping[column](item));
                } else {
                    data.push(item[column]);
                }
            }

            csvRows.push(data);
        }

        this.$export.hasFinishedExport = true;
        this.$export.dataUri = encodeURI(
            "data:text/csv;charset=utf-8," +
                csvRows.map((row) => row.join(this.getDelimiter())).join("\n")
        );
    }

    canExport() {
        console.log('canExport');
        return (
            Object.values(this.$export.columns).filter((enabled) => enabled)
                .length !== 0
        );
    }

    timeSinceEpoch = (date) => {
        const epoch = dayjs(this.$export.network.epoch);

        return date.unix() - epoch.unix();
    }

    getColumns = () => {
        let columns = this.$export.columns;
        if (columns.amount && columns.fee) {
            columns.total = true;
        }

        if (columns.amountFiat && columns.feeFiat) {
            columns.totalFiat = true;
        }

        const csvColumnsNames = Object.keys(this.csvColumns);
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

    getColumnTitles = () => {
        return Object.entries(this.getColumns()).map(([column]) => {
            return this.translateColumnCurrency(this.csvColumns[column]);
        });
    }

    translateColumnCurrency = (column) => {
        return column
            .replace(/:userCurrency/, this.userCurrency)
            .replace(/:networkCurrency/, this.$export.network.currency);
    }

    getDelimiter = () => {
        return this.delimiterMapping[this.$export.delimiter] || ",";
    }

    resetStatus = () => {
        this.$export.dataUri = null;
        this.$export.errorMessage = null;
        this.$export.successMessage = null;
        this.$export.hasFinishedExport = false;
    }

    performExport = () => {
        throw new Error('[Method not implemented]');
    }

    requestData = () => {
        throw new Error('[Method not implemented]');
    }
};
