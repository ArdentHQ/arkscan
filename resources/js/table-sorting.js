const TableSorting = (sortBy = "", sortDirection = "asc") => {
    return {
        sortBy,
        sortAsc: sortDirection === "asc",

        init() {
            this.sort(this.$refs[this.sortBy]);
        },

        sortByColumn($event) {
            const header = $event.target.closest('th');
            if (this.sortBy === header.getAttribute('x-ref')) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortBy = header.getAttribute('x-ref');
                this.sortAsc = (header.dataset['initialSort'] ?? 'asc') === 'asc';
            }
            console.log('wot');


            this.sort(header);
        },

        sort(element) {
            console.log(this.sortBy, this.sortAsc);

            this.getTableRows()
                .sort(
                    this.sortCallback(
                        Array.from(element.parentNode.children).indexOf(
                            element
                        )
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
            if (typeof row.children[index].dataset['value'] !== 'undefined') {
                return row.children[index].dataset['value'] ?? 0;
            }

            return row.children[index].innerText;
        },

        sortCallback(index) {
            return (a, b) =>
                ((row1, row2) => {
                    return row1 !== "" &&
                        row2 !== "" &&
                        !isNaN(row1) &&
                        !isNaN(row2)
                        ? row1 - row2
                        : row1.toString().localeCompare(row2);
                })(
                    this.getCellValue(this.sortAsc ? a : b, index),
                    this.getCellValue(this.sortAsc ? b : a, index)
                );
        },
    };
};

export default TableSorting;
