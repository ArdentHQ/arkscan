'use client';

import { createContext, useContext } from "react";
import { IPageHandlerContextType } from "./types";

const PageHandlerContext = createContext<IPageHandlerContextType | null>(null);

export function usePageHandler() {
    const context = useContext(PageHandlerContext);
    if (! context) {
        throw new Error("usePageHandler must be used within a PageHandlerProvider");
    }

    return context;
}

export default PageHandlerContext;
