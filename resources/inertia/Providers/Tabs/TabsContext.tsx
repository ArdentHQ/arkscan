'use client';

import { createContext, useContext } from "react";
import { ITabsContextType } from "./types";

const TabsContext = createContext<ITabsContextType | null>(null);

export function useTabs() {
    const context = useContext(TabsContext);
    if (! context) {
        throw new Error("useTabs must be used within a TabsProvider");
    }

    return context;
}

export default TabsContext;
