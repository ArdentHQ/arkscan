function FavoriteValidators() {
    const favorites = JSON.parse(localStorage.getItem("favorite-validators")) ?? [];

    return Alpine.reactive({
        favorites,

        toggle(publicKey) {
            let isFavoriteValue = !this.isFavorite(publicKey);

            if (isFavoriteValue) {
                this.favorites.push(publicKey);
            } else {
                this.favorites.splice(this.favorites.indexOf(publicKey), 1);
            }

            this.save();

            return isFavoriteValue;
        },

        isFavorite(publicKey) {
            return this.favorites.includes(publicKey);
        },

        save() {
            localStorage.setItem("favorite-validators", JSON.stringify(this.favorites));
        },
    });
}

export default FavoriteValidators();
