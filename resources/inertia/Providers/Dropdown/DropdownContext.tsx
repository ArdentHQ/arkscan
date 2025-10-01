'use client';

import { createContext, useContext } from "react";
import { IDropdownContextType } from "./types";

const DropdownContext = createContext<IDropdownContextType | null>(null);

export function useDropdown() {
    const context = useContext(DropdownContext);
    if (! context) {
        throw new Error("useDropdown must be used within a DropdownProvider");
    }

    return context;
}

export default DropdownContext;
