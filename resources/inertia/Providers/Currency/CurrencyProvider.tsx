import { useEffect, useState } from "react";
import WebhooksContext from "./CurrencyContext";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";
import { router } from "@inertiajs/react";
import { IPriceTickerData } from "@/types/generated";

export default function CurrencyProvider({
    children,
    tickerData,
}: {
    children: React.ReactNode;
    tickerData: IPriceTickerData;
}) {
    const [currentTickerData, setCurrentTickerData] = useState(tickerData);
    const [isUpdatingCurrency, setIsUpdatingCurrency] = useState(false);

    const { listen } = useWebhooks();

    const reloadPriceTicker = () => {
        setIsUpdatingCurrency(true);
        router.reload({
            only: ["priceTickerData"],
            showProgress: false,
            onFinish: () => {
                setIsUpdatingCurrency(false);
            },
        });
    };

    router.on("success", (event) => {
        setCurrentTickerData(event.detail.page.props.priceTickerData as IPriceTickerData);
    });

    useEffect(() => {
        return listen(`currency-update.${currentTickerData.currency}`, "CurrencyUpdate", reloadPriceTicker);
    }, [currentTickerData.currency]);

    const updateCurrency = (newCurrency: string): Promise<void> => {
        setIsUpdatingCurrency(true);
        return new Promise((resolve, reject) => {
            router.post(
                "/currency/update",
                { currency: newCurrency },
                {
                    only: ["priceTickerData"],
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
                currency: currentTickerData.currency,
                updateCurrency,
                isUpdatingCurrency,
                isPriceAvailable: currentTickerData.isPriceAvailable,
                priceExchangeRate: currentTickerData.priceExchangeRate,
            }}
        >
            {children}
        </WebhooksContext.Provider>
    );
}
