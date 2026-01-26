import { useState } from "react";
import PageHandlerContext from "./PageHandlerContext";
import { CancelToken } from "@inertiajs/core";

export default function PageHandlerProvider({ children }: { children: React.ReactNode }) {
    const [refreshPageHandler, setRefreshPage] =
        useState<(callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => void>();

    const [isLoading, setIsLoading] = useState(false);

    const value = {
        isLoading,
        setIsLoading,
        setRefreshPage: (callback: CallableFunction) => {
            setRefreshPage(() => callback);
        },
        refreshPage: (callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
            if (!refreshPageHandler) {
                return;
            }

            refreshPageHandler(callback, onCancelToken);
        },
    };

    return <PageHandlerContext.Provider value={value}>{children}</PageHandlerContext.Provider>;
}
