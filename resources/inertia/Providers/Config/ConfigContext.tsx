'use client';

import { createContext, useContext } from "react";
import { IConfigContextType } from "./types";

const ConfigContext = createContext<IConfigContextType | null>(null);

export function useConfig() {
    const context = useContext(ConfigContext);
    if (! context) {
        throw new Error("useConfig must be used within a ConfigProvider");
    }

    return context;
}

export default ConfigContext;
