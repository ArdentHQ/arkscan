"use client";

import { createContext, useContext } from "react";
import { IFilterContextType } from "./types";

const FilterContext = createContext<IFilterContextType | null>(null);

export function useFilter() {
    const context = useContext(FilterContext);
    if (!context) {
        throw new Error("useFilter must be used within a FilterProvider");
    }

    return context;
}

export default FilterContext;
