import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { ITab } from "@/Providers/Tabs/types";
import { CancelToken } from "@inertiajs/core";
import { router } from "@inertiajs/react";
import { useEffect, useRef } from "react";

export function useTabPolling(
    pollCurrentTab: (
        tab: string,
        callback?: CallableFunction,
        onCancelToken?: (onCancelToken: CancelToken) => void,
    ) => void,
) {
    const pollingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const { setRefreshPage } = usePageHandler();
    const { currentTab, onTabChange } = useTabs();

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        router.on("success", () => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollCurrentTab(currentTab), 8000);
        });

        if (!pollingTimerRef.current) {
            pollingTimerRef.current = setTimeout(() => pollCurrentTab(currentTab), 8000);

            pollCurrentTab(currentTab);
        }

        onTabChange((tab: ITab, isFirstLoad: boolean) => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollCurrentTab(tab.value), 8000);

            if (isFirstLoad) {
                pollCurrentTab(tab.value);
            }
        });

        setRefreshPage((callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
            pollCurrentTab(currentTab, callback, onCancelToken);
        });

        return () => {
            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, [currentTab]);
}
