"use client";

import { PageProps } from "@inertiajs/core";
import { createContext, useContext } from "react";

const ConfigContext = createContext<PageProps | null>(null);

export function useConfig() {
    const context = useContext(ConfigContext);
    if (!context) {
        throw new Error("useConfig must be used within a ConfigProvider");
    }

    return context;
}

export default ConfigContext;
