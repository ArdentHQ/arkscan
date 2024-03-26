import FavoriteValidators from "./favorite-validators.js";

const Validator = (publicKey, xData = {}) => {
    return Alpine.reactive({
        publicKey,

        get isFavorite() {
            return FavoriteValidators.isFavorite(this.publicKey);
        },

        set isFavorite(value) {
            this.toggleFavorite(value);
        },

        toggleFavorite() {
            FavoriteValidators.toggle(this.publicKey);

            this.$nextTick(() => {
                this.$dispatch("updateTableSorting");
            });
        },

        ...xData,
    });
};

export default Validator;
