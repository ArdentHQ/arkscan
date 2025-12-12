const Search = {
    setup(data) {
        return {
            ...data,
            blurHandler(event) {
                const blurredOutside = !this.$refs.search.contains(event.relatedTarget);

                if (blurredOutside) {
                    this.$wire.call("clear");
                }
            },
        };
    },
};

export default Search;
