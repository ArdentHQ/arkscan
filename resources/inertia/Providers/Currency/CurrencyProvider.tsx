import { useEffect, useState } from "react";
import WebhooksContext from "./CurrencyContext";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";
import { router } from "@inertiajs/react";

export default function CurrencyProvider({ children, currency }: { children: React.ReactNode; currency: string }) {
    const [currentCurrency, setCurrentCurrency] = useState(currency);
    const [isUpdatingCurrency, setIsUpdatingCurrency] = useState(false);

    const { listen } = useWebhooks();

    const reloadPriceTicker = () => {
        setIsUpdatingCurrency(true);
        router.reload({
            only: ["currency"],
            showProgress: false,
            onFinish: () => {
                setIsUpdatingCurrency(false);
            },
        });
    };

    router.on("success", (event) => {
        setCurrentCurrency(event.detail.page.props.currency as string);
    });

    useEffect(() => {
        return listen(`currency-update.${currentCurrency}`, "CurrencyUpdate", reloadPriceTicker);
    }, [currentCurrency]);

    const updateCurrency = (newCurrency: string): Promise<void> => {
        setIsUpdatingCurrency(true);
        return new Promise((resolve, reject) => {
            router.post(
                "/currency/update",
                { currency: newCurrency },
                {
                    only: ["currency"],
                    showProgress: false,
                    onSuccess: () => {
                        resolve();
                    },
                    onError: (error) => {
                        reject(error);
                    },
                    onFinish: () => {
                        setIsUpdatingCurrency(false);
                    },
                },
            );
        });
    };

    return (
        <WebhooksContext.Provider
            value={{
                currency: currentCurrency,
                updateCurrency,
                isUpdatingCurrency,
            }}
        >
            {children}
        </WebhooksContext.Provider>
    );
}
