import { sortRow } from "./includes/sorting";

// Variation of https://codepen.io/ryangjchandler/pen/WNQQKeR
const TableSorting = (
    tableId,
    componentId,
    sortBy = "",
    sortDirection = "asc",
    secondarySortBy = null,
    secondarySortDirection = "asc"
) => {
    return {
        sortBy,
        sortAsc: sortDirection === "asc",
        secondarySortIndex: null,
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

                    if (!this.$refs[this.sortBy]) {
                        if (this.windowEvent) {
                            window.removeEventListener(
                                "updateTableSorting",
                                this.windowEvent
                            );

                            this.windowEvent = null;
                        }

                        return;
                    }

                    toEl.querySelectorAll("table tbody").forEach((tbody) => {
                        Alpine.morph(
                            tbody,
                            this.update(tbody.cloneNode(true)).outerHTML
                        );
                    });
                });
            }
        },

        update(table) {
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
                if (typeof row.dataset["rowIndex"] === "undefined") {
                    row.dataset["rowIndex"] = index;
                }
            });

            return this.sort(this.$refs[this.sortBy], table);
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

        table() {
            return this.$el.closest(`#${tableId}`).querySelector("tbody");
        },

        sort(element, table) {
            if (table === undefined) {
                table = this.table();
            }

            this.getTableRows(table)
                .sort(
                    this.sortCallback(
                        Array.from(element.parentNode.children).indexOf(element)
                    )
                )
                .forEach((tr) => {
                    table.appendChild(tr);
                });

            return table;
        },

        getTableRows(tbody) {
            if (tbody !== undefined) {
                return Array.from(tbody.querySelectorAll("tr"));
            }

            return Array.from(this.table().querySelectorAll("tbody tr"));
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
            return sortRow(
                row1,
                row2,
                this.getCellValue(row1, index),
                this.getCellValue(row2, index),
                sortAscending,
                sortByRowIndex
            );
        },
    };
};

export default TableSorting;
