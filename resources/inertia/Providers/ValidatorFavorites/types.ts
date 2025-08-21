export interface ValidatorFavoritesContextType {
    favorites: string[];
    isFavorite: (publicKey: string) => boolean;
    toggleFavorite: (publicKey: string) => void;
}
