import FavoriteDelegates from "./favorite-delegates.js";

const Delegate = (publicKey, xData = {}) => {
    return Alpine.reactive({
        publicKey,

        get isFavorite() {
            return FavoriteDelegates.isFavorite(this.publicKey);
        },

        set isFavorite(value) {
            this.toggleFavorite(value);
        },

        toggleFavorite() {
            FavoriteDelegates.toggle(this.publicKey);

            this.$nextTick(() => {
                this.$dispatch("updateTableSorting");
            });
        },

        ...xData,
    });
};

export default Delegate;
