import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { useTabs } from "@/Providers/Tabs/TabsContext";
import { ITab } from "@/Providers/Tabs/types";
import { router } from "@inertiajs/react";
import { useEffect, useRef } from "react";

export function useTabPolling(pollCurrentTab: (tab: string, callback?: CallableFunction) => void) {
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

        setRefreshPage((callback?: CallableFunction) => {
            pollCurrentTab(currentTab, callback);
        });

        return () => {
            if (!pollingTimerRef.current) {
                return;
            }

            clearTimeout(pollingTimerRef.current);
        };
    }, [currentTab]);
}
