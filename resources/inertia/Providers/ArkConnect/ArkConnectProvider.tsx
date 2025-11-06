"use client";

import { useState } from "react";
import ArkConnectContext from "./ArkConnectContext";

export default function ArkConnectProvider({ children }: { children: React.ReactNode }) {
    const [isOpen, setIsOpen] = useState(false);

    const value = {
        isOpen,
        setIsOpen,
    };

    return <ArkConnectContext.Provider value={value}>{children}</ArkConnectContext.Provider>;
}
