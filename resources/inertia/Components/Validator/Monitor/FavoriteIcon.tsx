import classNames from "classnames";

// @ts-ignore
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import { useEffect, useState } from "react";
import FavoriteStarIcon from "@icons/favorite-star.svg?react";

export default function FavoriteIcon({ validator, label }: { validator: any; label?: string }) {
    const { favorites, isFavorite, toggleFavorite } = useValidatorFavorites();
    const [isFavorited, setIsFavorited] = useState(false);

    useEffect(() => {
        setIsFavorited(isFavorite(validator.wallet.public_key));
    }, [favorites]);

    return (
        <div>
            <button
                type="button"
                className={classNames({
                    "favorite-icon flex items-center space-x-2 font-semibold": true,
                    "dark:text-theme-dark-300": !isFavorited,
                    "favorite-icon__selected text-theme-primary-600": isFavorited,
                })}
                onClick={() => {
                    toggleFavorite(validator.wallet.public_key);
                }}
            >
                <FavoriteStarIcon name="favorite-star-icon" className="w-[20px]" />

                {label && <span className="text-sm leading-4.25">{label}</span>}
            </button>
        </div>
    );
}
