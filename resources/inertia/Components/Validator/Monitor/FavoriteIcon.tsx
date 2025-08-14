import classNames from "@/utils/class-names";

// @ts-ignore
import FavoriteIconSvg from "@icons/favorite-star.svg";
import { useValidatorFavorites } from "@/Providers/ValidatorFavorites/ValidatorFavoritesContext";
import { useEffect, useState } from "react";

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
                    "flex items-center space-x-2 font-semibold favorite-icon": true,
                    'dark:text-theme-dark-300': ! isFavorited,
                    'text-theme-primary-600 favorite-icon__selected': isFavorited,
                })}
                onClick={() => {
                    toggleFavorite(validator.wallet.public_key);
                }}
            >
                <FavoriteIconSvg width={20} />

                {label && (
                    <span className="text-sm leading-4.25">
                        {label}
                    </span>
                )}
            </button>
        </div>
    );
}
