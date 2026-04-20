"use client";

import { createContext, useContext } from "react";
import { ITableSortingContextType } from "./types";

const TableSortingContext = createContext<ITableSortingContextType | null>(null);

export function useTableSorting() {
    const context = useContext(TableSortingContext);
    if (!context) {
        throw new Error("useTableSorting must be used within a TableSortingProvider");
    }

    return context;
}

export default TableSortingContext;
