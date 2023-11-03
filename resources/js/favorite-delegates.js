class FavoriteDelegates {
    favorites = [];

    constructor() {
        this.favorites =
            JSON.parse(localStorage.getItem("favorite-delegates")) ?? [];
    }

    toggle(publicKey, value) {
        let isFavoriteValue = !this.isFavorite(publicKey);
        if (value !== null) {
            isFavoriteValue = value;
        }

        if (isFavoriteValue) {
            this.favorites.push(publicKey);
        } else {
            this.favorites.splice(this.favorites.indexOf(publicKey), 1);
        }

        this.save();

        return isFavoriteValue;
    }

    isFavorite(publicKey) {
        return this.favorites.includes(publicKey);
    }

    save() {
        localStorage.setItem(
            "favorite-delegates",
            JSON.stringify(this.favorites)
        );
    }
}

export default new FavoriteDelegates();
