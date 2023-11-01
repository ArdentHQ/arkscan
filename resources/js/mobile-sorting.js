// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const MobileSorting = (
    sortBy = "",
    sortDirection = "asc",
    secondarySortBy = null,
    secondarySortDirection = "asc"
) => {
    return {
        livewireHook: null,

        init() {
            this.update();

            if (typeof Livewire !== "undefined") {
                this.livewireHook = Livewire.hook("message.processed", () => {
                    console.log(this.$el);
                    // if (!this.$refs[sortBy]) {
                    //     if (this.livewireHook) {
                    //         this.livewireHook();
                    //     }

                    //     return;
                    // }

                    this.update();
                });
            }
        },

        update() {
            this.getRows().forEach((row, index) => {
                row.dataset["rowIndex"] = index;
            });

            this.sort();
        },

        sort() {
            this.getRows()
                .sort(
                    this.sortCallback(sortBy)
                )
                .forEach((row) => {
                    this.$el.appendChild(row);
                });
        },

        getRows() {
            return Array.from(this.$el.children);
        },

        getValue(row, sortBy) {
            if (! row.dataset[sortBy]) {
                return null;
            }

            return row.dataset[sortBy];
        },

        sortCallback(sortBy) {
            return (row1, row2) => {
                const sortResult = this.sortRows(
                    row1,
                    row2,
                    sortBy,
                    sortDirection === "asc"
                );
                if (sortResult === 0 && secondarySortBy !== null) {
                    return this.sortRows(
                        row1,
                        row2,
                        secondarySortBy,
                        secondarySortDirection === "asc",
                        false
                    );
                }

                return sortResult;
            };
        },

        sortRows(row1, row2, sortBy, sortAscending, sortByRowIndex = true) {
            const row1Value = this.getValue(row1, sortBy);
            const row2Value = this.getValue(row2, sortBy);

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

export default MobileSorting;
