const Pagination = (pageName, lastPage) => ({
    search: false,
    page: null,
    init() {
        this.$watch("search", (search) => {
            if (search) {
                this.$nextTick(() => {
                    const searchInputs = this.$el.querySelectorAll(
                        `input[name=${pageName}]`
                    );
                    searchInputs.forEach((input) => {
                        input.focus();
                    });
                });
            } else {
                this.page = null;
            }
        });

        this.$watch("page", (page) => {
            if (page === null) {
                return;
            }

            if (page < 1) {
                this.page = 1;
            }
            if (page > lastPage) {
                this.page = lastPage;
            }
        });
    },
    blurHandler() {
        if (!this.page) {
            this.search = false;
        }
    },
    toggleSearch() {
        this.search = !this.search;
    },
    hideSearch() {
        this.search = false;
    },
});

export default Pagination;
