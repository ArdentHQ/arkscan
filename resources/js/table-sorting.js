// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const TableSorting = (sortBy = "", sortDirection = "asc") => {
    return {
        sortBy,
        sortAsc: sortDirection === "asc",

        init() {
            this.getTableRows().forEach((row, index) => {
                row.dataset['rowIndex'] = index;
            });

            this.sort(this.$refs[this.sortBy]);
        },

        sortByColumn($event) {
            const header = $event.target.closest("th");
            if (this.sortBy === header.getAttribute("x-ref")) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortBy = header.getAttribute("x-ref");
                this.sortAsc =
                    (header.dataset["initialSort"] ?? "asc") === "asc";
            }

            this.sort(header);
        },

        sort(element) {
            this.getTableRows()
                .sort(
                    this.sortCallback(
                        Array.from(element.parentNode.children).indexOf(element)
                    )
                )
                .forEach((tr) => {
                    this.$refs.tbody.appendChild(tr);
                });
        },

        getTableRows() {
            return Array.from(this.$refs.tbody.querySelectorAll("tr"));
        },

        getCellValue(row, index) {
            if (typeof row.children[index].dataset["value"] !== "undefined") {
                return row.children[index].dataset["value"] ?? null;
            }

            return row.children[index].innerText;
        },

        sortCallback(index) {
            return (a, b) =>
                ((row1, row2) => {
                    const row1Value = this.getCellValue(row1, index);
                    const row2Value = this.getCellValue(row2, index);

                    const isRow1Numeric =
                        row1Value !== "" && !isNaN(row1Value) && row1Value !== null;
                    const isRow2Numeric =
                        row2Value !== "" && !isNaN(row2Value) && row2Value !== null;

                    if (isNaN(row1Value) && isNaN(row2Value)) {
                        return row1Value.toString().localeCompare(row2Value);
                    }

                    if (isRow1Numeric && isRow2Numeric) {
                        return row1Value - row2Value;
                    }

                    if (isRow1Numeric && !isRow2Numeric) {
                        return 0;
                    }

                    if (row1Value === row2Value) {
                        return this.sortAsc ? row1.dataset['rowIndex'] - row2.dataset['rowIndex'] : row2.dataset['rowIndex'] - row1.dataset['rowIndex'];
                    }

                    return this.sortAsc ? 1 : -1;
                })(
                    this.sortAsc ? a : b,
                    this.sortAsc ? b : a
                );
        },
    };
};

export default TableSorting;
