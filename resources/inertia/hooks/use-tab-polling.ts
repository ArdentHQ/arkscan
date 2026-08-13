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
    enabled = true,
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

        pollWithCancelToken(currentTab);

        setRefreshPage((callback?: CallableFunction, onCancelToken?: (onCancelToken: CancelToken) => void) => {
            pollWithCancelToken(currentTab, callback, onCancelToken);
        });

        setCancelPolling(cancelPolling);

        if (!enabled) {
            onTabChange((tab: ITab, isFirstLoad: boolean) => {
                if (isFirstLoad) {
                    pollWithCancelToken(tab.value);
                }
            });

            return cancelPolling;
        }

        const removeSuccessListener = router.on("success", () => {
            if (pollingTimerRef.current) {
                clearTimeout(pollingTimerRef.current);
            }

            pollingTimerRef.current = setTimeout(() => pollWithCancelToken(currentTab), 8000);
        });

        pollingTimerRef.current = setTimeout(() => pollWithCancelToken(currentTab), 8000);

        onTabChange((tab: ITab, isFirstLoad: boolean) => {
            cancelPolling();

            pollingTimerRef.current = setTimeout(() => pollWithCancelToken(tab.value), 8000);

            if (isFirstLoad) {
                pollWithCancelToken(tab.value);
            }
        });

        return () => {
            removeSuccessListener();
            cancelPolling();
        };
    }, [currentTab, enabled]);
}
