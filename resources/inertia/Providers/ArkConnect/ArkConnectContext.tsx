"use client";

import { createContext, useContext } from "react";
import { IArkConnectContextType } from "./types";

const ArkConnectContext = createContext<IArkConnectContextType | null>(null);

export function useArkConnect() {
    const context = useContext(ArkConnectContext);
    if (!context) {
        throw new Error("useArkConnect must be used within a ArkConnectProvider");
    }

    return context;
}

export default ArkConnectContext;
