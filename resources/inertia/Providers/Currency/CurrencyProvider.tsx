import { useCallback, useEffect, useState } from "react";
import WebhooksContext from "./CurrencyContext";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";
import { router } from "@inertiajs/react";

export default function CurrencyProvider({ children, currency }: { children: React.ReactNode; currency: string }) {
    const [currentCurrency, setCurrentCurrency] = useState(currency);

    const { listen } = useWebhooks();

    const reloadPriceTicker = useCallback(() => {
        router.reload({
            only: ["currency"],
        });
    }, []);

    router.on("success", (event) => {
        setCurrentCurrency(event.detail.page.props.currency as string);
    });

    useEffect(() => {
        return listen(`currency-update.${currentCurrency}`, "CurrencyUpdate", reloadPriceTicker);

        // @TODO: see for alternatives for handling this the the currency Livewire component
        // is removed https://app.clickup.com/t/86d ye0rvv
        // @see `resources/views/components/webhooks/currency-update.blade.php`
        // window.Livewire.on("currencyChanged", (currency: string) => {
        //     remove(`currency-update.${currentCurrency}`, "CurrencyUpdate", reloadPriceTicker);
        //     listen(`currency-update.${currency}`, "CurrencyUpdate", reloadPriceTicker);

        //     setCurrentCurrency(currency);
        // });
    }, [currentCurrency]);

    return (
        <WebhooksContext.Provider
            value={{
                currency: currentCurrency,
            }}
        >
            {children}
        </WebhooksContext.Provider>
    );
}
