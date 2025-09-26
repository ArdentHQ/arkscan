'use client';

import { createContext, useContext } from "react";
import { INetworkContextType } from "./types";

const NetworkContext = createContext<INetworkContextType | null>(null);

export function useNetwork() {
    const context = useContext(NetworkContext);
    if (! context) {
        throw new Error("useNetwork must be used within a NetworkProvider");
    }

    return context;
}

export default NetworkContext;
