'use client';

import { useEffect, useState } from "react";
import ValidatorFavoritesContext from "./ValidatorFavoritesContext";

export default function ValidatorFavoritesProvider({ children }: { children: React.ReactNode }) {
    const [favorites, setFavorites] = useState<string[]>([]);

    useEffect(() => {
        setFavorites(JSON.parse(localStorage.getItem("favorite-validators") || "[]"));
    }, []);

    const isFavorite = (publicKey: string) => {
        if (! favorites) {
            return false;
        }

        return favorites.includes(publicKey);
    }

    const save = (updatedFavorites: string[]) => {
        setFavorites(updatedFavorites);

        localStorage.setItem(
            "favorite-validators",
            JSON.stringify(updatedFavorites)
        );
    }

    const toggleFavorite = (publicKey: string) => {
        let isFavoriteValue = isFavorite(publicKey);
        const updatedFavorites = [...favorites];
        if (isFavoriteValue) {
            updatedFavorites.splice(updatedFavorites.indexOf(publicKey), 1);
        } else {
            updatedFavorites.push(publicKey);
        }

        save(updatedFavorites);
    }

    const value = {
        favorites,
        isFavorite,
        toggleFavorite,
    };

    return (
        <ValidatorFavoritesContext.Provider value={value}>
            {children}
        </ValidatorFavoritesContext.Provider>
    );
};
