import Alpine from "alpinejs";

function FavoriteDelegates() {
    const favorites = JSON.parse(localStorage.getItem("favorite-delegates")) ?? [];

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
            localStorage.setItem(
                "favorite-delegates",
                JSON.stringify(this.favorites)
            );
        },
    });
}

export default FavoriteDelegates();
