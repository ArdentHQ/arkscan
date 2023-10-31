// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const TableSorting = (
    sortBy = "",
    sortDirection = "asc",
    secondarySortBy = null,
    secondarySortDirection = "asc"
) => {
    return {
        sortBy,
        sortAsc: sortDirection === "asc",
        secondarySortIndex: null,
        livewireHook: null,

        init() {
            this.update();

            if (typeof Livewire !== "undefined") {
                this.livewireHook = Livewire.hook("message.processed", () => {
                    if (!this.$refs[this.sortBy]) {
                        if (this.livewireHook) {
                            this.livewireHook();
                        }

                        return;
                    }

                    this.update();
                });
            }
        },

        update() {
            this.secondarySortIndex = null;
            if (secondarySortBy) {
                const secondaryElement = this.$refs[secondarySortBy];

                if (secondaryElement) {
                    this.secondarySortIndex = Array.from(
                        secondaryElement.parentNode.children
                    ).indexOf(secondaryElement);
                }
            }

            this.getTableRows().forEach((row, index) => {
                row.dataset["rowIndex"] = index;
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
            return (row1, row2) => {
                const sortResult = this.sortRows(
                    row1,
                    row2,
                    index,
                    this.sortAsc
                );
                if (sortResult === 0 && this.secondarySortIndex !== null) {
                    return this.sortRows(
                        row1,
                        row2,
                        this.secondarySortIndex,
                        secondarySortDirection === "asc",
                        false
                    );
                }

                return sortResult;
            };
        },

        sortRows(row1, row2, index, sortAscending, sortByRowIndex = true) {
            const row1Value = this.getCellValue(row1, index);
            const row2Value = this.getCellValue(row2, index);

            const isRow1Numeric =
                row1Value !== "" && !isNaN(row1Value) && row1Value !== null;
            const isRow2Numeric =
                row2Value !== "" && !isNaN(row2Value) && row2Value !== null;

            if (isNaN(row1Value) && isNaN(row2Value)) {
                return sortAscending
                    ? row2Value.toString().localeCompare(row1Value)
                    : row1Value.toString().localeCompare(row2Value);
            }

            if (isRow1Numeric && isRow2Numeric) {
                return sortAscending
                    ? row1Value - row2Value
                    : row2Value - row1Value;
            }

            if (isRow1Numeric && !isRow2Numeric) {
                return 0;
            }

            if (sortByRowIndex && row1Value === row2Value) {
                return sortAscending
                    ? row2.dataset["rowIndex"] - row1.dataset["rowIndex"]
                    : row1.dataset["rowIndex"] - row2.dataset["rowIndex"];
            }

            return sortAscending ? 1 : -1;
        },
    };
};

export default TableSorting;
