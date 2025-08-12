'use client';

import { createContext, useContext } from "react";
import { ValidatorFavoritesContextType } from "./types";

const ValidatorFavoritesContext = createContext<ValidatorFavoritesContextType | null>(null);

export function useValidatorFavorites() {
    const context = useContext(ValidatorFavoritesContext);
    if (! context) {
        throw new Error("useValidatorFavorites must be used within a ValidatorFavoritesProvider");
    }

    return context;
}

export default ValidatorFavoritesContext;
