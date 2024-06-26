import { sortRow } from "./includes/sorting";

// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const MobileSorting = (
    sortBy = "",
    sortDirection = "asc",
    secondarySortBy = null,
    secondarySortDirection = "asc"
) => {
    return {
        livewireHook: null,
        windowEvent: null,

        init() {
            this.$nextTick(() => {
                this.update();
            });

            this.windowEvent = this.update.bind(this);

            window.addEventListener("updateTableSorting", this.windowEvent);

            if (typeof Livewire !== "undefined") {
                this.livewireHook = Livewire.hook("commit", ({ succeed }) => {
                    succeed(() => {
                        this.update();
                    });
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
                .sort(this.sortCallback(sortBy))
                .forEach((row) => {
                    this.$el.appendChild(row);
                });
        },

        getRows() {
            return Array.from(this.$el.children);
        },

        getValue(row, sortBy) {
            if (!row.dataset[sortBy]) {
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
            return sortRow(
                row1,
                row2,
                this.getValue(row1, sortBy),
                this.getValue(row2, sortBy),
                sortAscending,
                sortByRowIndex
            );
        },
    };
};

export default MobileSorting;
