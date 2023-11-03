import FavoriteDelegates from "./favorite-delegates.js";

const favorites = JSON.parse(localStorage.getItem('favorite-delegates')) ?? [];

const Delegate = (publicKey, xData = {}) => {
    const isFavorite = favorites.includes(publicKey);

    return {
        publicKey,
        isFavorite,
        favorites,
        // isFavoriteValue: FavoriteDelegates.isFavorite(publicKey),
        // isFavorite: FavoriteDelegates.isFavorite(publicKey),

        // init() {
        //     this.$watch('favorites', () => {
        //         console.log('asdasd', this.favorites);
        //     });
        // },

        // get isFavorite() {
        //     return this.isFavoriteValue;
        // },

        // set isFavorite(value) {
        //     this.toggleFavorite(value);
        // },

        toggleFavorite(value = null) {
            this.isFavorite = FavoriteDelegates.toggle(this.publicKey, value);

            this.$nextTick(() => {
                this.$dispatch('updateTableSorting');
            });

            // this.isFavoriteValue = ! this.isFavoriteValue;
            // if (value !== null) {
            //     this.isFavoriteValue = value;
            // }

            // if (this.isFavoriteValue) {
            //     this.favorites.push(this.publicKey);
            // } else {
            //     this.favorites.splice(this.favorites.indexOf(this.publicKey), 1);
            // }

            // localStorage.setItem('favorite-delegates', JSON.stringify(favorites));
        },

        ...xData,
    };
}

export default Delegate;
