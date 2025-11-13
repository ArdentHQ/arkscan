import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import WebhooksContext, { IWebhooksContext, WebhookHandler } from "./WebhooksContext";

type ListenerRegistry = Record<string, Record<string, Set<WebhookHandler>>>;

export default function WebhooksProvider({
    children,
    broadcasting,
    currency,
}: {
    children: React.ReactNode;
    broadcasting: string;
    currency: string;
}) {
    const enabled = broadcasting === "reverb";
    const [currentCurrency, setCurrentCurrency] = useState(currency);
    const listenersRef = useRef<ListenerRegistry>({});

    const getEcho = () => {
        return window.Echo!;
    };

    const remove = useCallback<IWebhooksContext["remove"]>(
        (channel, event, handler) => {
            const echo = getEcho();

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
            if (!enabled) {
                return (): void => undefined;
            }

            const echo = getEcho();

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

    const reloadPriceTicker = useCallback(() => {
        window.Livewire.emit("reloadPriceTicker");
    }, []);

    useEffect(() => {
        listen(`currency-update.${currentCurrency}`, "CurrencyUpdate", reloadPriceTicker);

        // @TODO: see for alternatives for handling this the the currency Livewire component
        // is removed https://app.clickup.com/t/86d ye0rvv
        // @see `resources/views/components/webhooks/currency-update.blade.php`
        // window.Livewire.on("currencyChanged", (currency: string) => {
        //     remove(`currency-update.${currentCurrency}`, "CurrencyUpdate", reloadPriceTicker);
        //     listen(`currency-update.${currency}`, "CurrencyUpdate", reloadPriceTicker);

        //     setCurrentCurrency(currency);
        // });

        return () => {
            remove(`currency-update.${currentCurrency}`, "CurrencyUpdate", reloadPriceTicker);
        };
    }, [currentCurrency]);

    const value = useMemo<IWebhooksContext>(
        () => ({
            listen,
            remove,
            enabled,
        }),
        [listen, remove],
    );

    return <WebhooksContext.Provider value={value}>{children}</WebhooksContext.Provider>;
}
