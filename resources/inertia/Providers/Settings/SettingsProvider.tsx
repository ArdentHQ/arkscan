import { useEffect, useState } from "react";
import SettingsContext from "./SettingsContext";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";
import { router } from "@inertiajs/react";
import { IPriceTickerData } from "@/types/generated";

export default function SettingsProvider({
    children,
    tickerData,
    theme,
}: {
    children: React.ReactNode;
    tickerData: IPriceTickerData;
    theme: string;
}) {
    const [currentTickerData, setCurrentTickerData] = useState(tickerData);
    const [isUpdatingCurrency, setIsUpdatingCurrency] = useState(false);
    const [currentTheme, setCurrentTheme] = useState(theme);
    const [isUpdatingTheme, setIsUpdatingTheme] = useState(false);

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

        setCurrentTheme(event.detail.page.props.theme as string);
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

    const updateTheme = (newTheme: string): Promise<void> => {
        setIsUpdatingTheme(true);

        // The theme can be updated immediately because there is no polling necessary.
        setCurrentTheme(newTheme);

        return new Promise((resolve, reject) => {
            router.post(
                "/theme/update",
                { theme: newTheme },
                {
                    only: ["theme"],
                    showProgress: false,
                    onSuccess: () => {
                        resolve();
                    },
                    onError: (error) => {
                        setCurrentTheme(currentTheme);

                        reject(error);
                    },
                    onFinish: () => {
                        setIsUpdatingTheme(false);
                    },
                },
            );
        });
    };

    return (
        <SettingsContext.Provider
            value={{
                currency: currentTickerData.currency,
                updateCurrency,
                isUpdatingCurrency,
                isPriceAvailable: currentTickerData.isPriceAvailable,
                priceExchangeRate: currentTickerData.priceExchangeRate,
                theme: currentTheme,
                updateTheme,
                isUpdatingTheme,
            }}
        >
            {children}
        </SettingsContext.Provider>
    );
}
