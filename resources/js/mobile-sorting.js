import { sortRow } from "./includes/sorting";

// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const MobileSorting = (
    tableId,
    componentId,
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

            this.windowEvent = () => {
                this.update();
            };

            window.addEventListener("updateTableSorting", this.windowEvent);

            if (typeof Livewire !== "undefined") {
                Livewire.hook("morph.updating", ({ component, toEl }) => {
                    if (component.name !== componentId) {
                        return;
                    }

                    if (
                        !toEl.getAttribute("wire:id") &&
                        toEl.getAttribute("id") !== tableId &&
                        !toEl.classList.contains("table-container")
                    ) {
                        return;
                    }

                    toEl.querySelectorAll(".table-list-mobile").forEach(
                        (tbody) => {
                            Alpine.morph(
                                tbody,
                                this.update(tbody.cloneNode(true)).outerHTML
                            );
                        }
                    );
                });
            }
        },

        update(table) {
            this.getRows().forEach((row, index) => {
                row.dataset["rowIndex"] = index;
            });

            return this.sort(table);
        },

        table() {
            return this.$el
                .closest(`#${tableId}`)
                .querySelector(".table-list-mobile");
        },

        sort(table) {
            if (table === undefined) {
                table = this.table();
            }

            this.getRows(table)
                .sort(this.sortCallback(sortBy))
                .forEach((row) => {
                    table.appendChild(row);
                });

            return table;
        },

        getRows(table) {
            if (table !== undefined) {
                return Array.from(table.children);
            }

            return Array.from(this.table().children);
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
