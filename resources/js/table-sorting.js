// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const TableSorting = (sortBy = "", sortDirection = "asc") => {
    return {
        sortBy,
        sortAsc: sortDirection === "asc",

        init() {
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
                    const isRow1Empty = row1 === "" || isNaN(row1) || row1 === null;
                    const isRow2Empty = row2 === "" || isNaN(row2) || row2 === null;

                    if (! isRow1Empty && ! isRow2Empty) {
                        return row1 - row2;
                    }

                    if (! isRow1Empty && isRow2Empty) {
                        return 0;
                    }

                    return this.sortAsc ? 1 : -1;
                })(
                    this.getCellValue(this.sortAsc ? a : b, index),
                    this.getCellValue(this.sortAsc ? b : a, index)
                );
        },
    };
};

export default TableSorting;
