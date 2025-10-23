"use client";

import { useState } from "react";
import PageHandlerContext from "./PageHandlerContext";

export default function PageHandlerProvider({ children }: { children: React.ReactNode }) {
    const [refreshPageHandler, setRefreshPage] = useState<(callback?: CallableFunction) => void>();

    const value = {
        setRefreshPage: (callback: CallableFunction) => {
            setRefreshPage(() => callback);
        },
        refreshPage: (callback?: CallableFunction) => {
            if (!refreshPageHandler) {
                return;
            }

            refreshPageHandler(callback);
        },
    };

    return <PageHandlerContext.Provider value={value}>{children}</PageHandlerContext.Provider>;
}
