'use client';

import { useState } from "react";
import DropdownContext from "./DropdownContext";

export default function DropdownProvider({ children }: { children: React.ReactNode }) {
    const [isOpen, setIsOpen] = useState(false);

    const value = {
        isOpen,
        setIsOpen,
    };

    return (
        <DropdownContext.Provider value={value}>
            {children}
        </DropdownContext.Provider>
    );
};
