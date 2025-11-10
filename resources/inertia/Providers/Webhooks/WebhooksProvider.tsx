import { useCallback, useMemo, useRef } from "react";
import WebhooksContext, { IWebhooksContext, WebhookHandler } from "./WebhooksContext";

declare global {
    interface Window {
        Echo?: {
            channel: (channel: string) => {
                listen: (event: string, callback: WebhookHandler) => void;
                stopListening: (event: string, callback: WebhookHandler) => void;
                subscribe?: () => void;
                unsubscribe?: () => void;
            };
            leave: (channel: string) => void;
        };
    }
}

type ListenerRegistry = Record<string, Record<string, Set<WebhookHandler>>>;

export default function WebhooksProvider({
    children,
    broadcasting,
}: {
    children: React.ReactNode;
    broadcasting: string;
}) {
    console.log("broadcasting", broadcasting);
    const listenersRef = useRef<ListenerRegistry>({});

    const getEcho = useCallback(() => {
        if (typeof window === "undefined") {
            return null;
        }

        return window.Echo ?? null;
    }, []);

    const remove = useCallback<IWebhooksContext["remove"]>(
        (channel, event, handler) => {
            const echo = getEcho();
            if (!echo) {
                return;
            }

            const channelListeners = listenersRef.current[channel];
            if (!channelListeners) {
                return;
            }

            const eventListeners = channelListeners[event];
            if (!eventListeners || !eventListeners.has(handler)) {
                return;
            }

            echo.channel(channel).stopListening(event, handler);
            eventListeners.delete(handler);

            if (eventListeners.size === 0) {
                delete channelListeners[event];
            }

            if (Object.keys(channelListeners).length === 0) {
                delete listenersRef.current[channel];
                echo.leave(channel);
            }
        },
        [getEcho],
    );

    const listen = useCallback<IWebhooksContext["listen"]>(
        (channel, event, handler) => {
            const echo = getEcho();
            if (!echo) {
                return () => undefined;
            }

            if (!listenersRef.current[channel]) {
                listenersRef.current[channel] = {};
            }

            if (!listenersRef.current[channel][event]) {
                listenersRef.current[channel][event] = new Set();
            }

            if (listenersRef.current[channel][event].has(handler)) {
                return () => undefined;
            }

            const subscription = echo.channel(channel);
            subscription.subscribe?.();
            subscription.listen(event, handler);

            listenersRef.current[channel][event].add(handler);

            return () => remove(channel, event, handler);
        },
        [getEcho, remove],
    );

    const value = useMemo<IWebhooksContext>(
        () => ({
            listen,
            remove,
            enabled: broadcasting === "reverb",
        }),
        [listen, remove],
    );

    return <WebhooksContext.Provider value={value}>{children}</WebhooksContext.Provider>;
}
