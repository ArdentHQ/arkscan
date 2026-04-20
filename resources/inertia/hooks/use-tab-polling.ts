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
    const pollingCancelTokenRef = useRef<CancelToken | null>(null);

    const { setRefreshPage, setCancelPolling } = usePageHandler();
    const { currentTab, onTabChange } = useTabs();

    const pollWithCancelToken = (
        tab: string,
        callback?: CallableFunction,
        onCancelToken?: (onCancelToken: CancelToken) => void,
    ) => {
        pollCurrentTab(tab, callback, (cancelToken) => {
            pollingCancelTokenRef.current = cancelToken;
            onCancelToken?.(cancelToken);
        });
    };

    const cancelPolling = () => {
        pollingCancelTokenRef.current?.cancel();
        pollingCancelTokenRef.current = null;

        if (pollingTimerRef.current) {
            clearTimeout(pollingTimerRef.current);
            pollingTimerRef.current = null;
        }
    };

    useEffect(() => {
        if (!currentTab) {
            return;
        }

        const removeSuccessListener = router.on("success", () => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollWithCancelToken(currentTab), 8000);
        });

        if (!pollingTimerRef.current) {
            pollingTimerRef.current = setTimeout(() => pollWithCancelToken(currentTab), 8000);

            pollWithCancelToken(currentTab);
        }

        onTabChange((tab: ITab, isFirstLoad: boolean) => {
            cancelPolling();

            pollingTimerRef.current = setTimeout(() => pollWithCancelToken(tab.value), 8000);

            if (isFirstLoad) {
                pollWithCancelToken(tab.value);
            }
        });

        setRefreshPage((callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
            pollWithCancelToken(currentTab, callback, onCancelToken);
        });

        setCancelPolling(cancelPolling);

        return () => {
            removeSuccessListener();
            cancelPolling();
        };
    }, [currentTab]);
}
