import { useEffect, useMemo, useState } from "react";
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
    const [isThemeTransitioning, setIsThemeTransitioning] = useState(false);

    const resolvedTheme = useMemo(() => {
        if (currentTheme === "auto" || !currentTheme) {
            return window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
        }

        return currentTheme;
    }, [currentTheme]);

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
                    preserveScroll: true,
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
        if (newTheme === currentTheme) {
            return Promise.resolve();
        }

        // Temporarily disable transitions
        setIsThemeTransitioning(true);
        document.documentElement.classList.add("theme-transitioning");

        setCurrentTheme(newTheme);
        localStorage.theme = newTheme;

        return new Promise((resolve, reject) => {
            router.post(
                "/theme/update",
                { theme: newTheme },
                {
                    only: ["theme"],
                    preserveScroll: true,
                    showProgress: false,
                    onSuccess: () => {
                        // Transitions will be re-enabled in the useEffect when currentTheme updates
                        resolve();
                    },
                    onError: (error) => {
                        setCurrentTheme(currentTheme);
                        localStorage.theme = currentTheme;

                        // Re-enable transitions on error
                        setIsThemeTransitioning(false);
                        document.documentElement.classList.remove("theme-transitioning");

                        reject(error);
                    },
                },
            );
        });
    };

    useEffect(() => {
        if (currentTheme === "dark") {
            document.documentElement.classList.add("dark");
            document.documentElement.classList.remove("light");
            document.documentElement.classList.remove("dim");
        } else if (currentTheme === "dim") {
            document.documentElement.classList.add("dim");
            document.documentElement.classList.add("dark");
            document.documentElement.classList.remove("light");
        } else {
            document.documentElement.classList.remove("dark");
            document.documentElement.classList.remove("dim");
        }

        // If we're transitioning themes, re-enable transitions after changes are applied
        if (isThemeTransitioning) {
            // Use requestAnimationFrame to ensure the DOM has been updated
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        setIsThemeTransitioning(false);
                        document.documentElement.classList.remove("theme-transitioning");
                    }, 50);
                });
            });
        }
    }, [currentTheme, isThemeTransitioning]);

    return (
        <SettingsContext.Provider
            value={{
                currency: currentTickerData.currency,
                updateCurrency,
                isUpdatingCurrency,
                isPriceAvailable: currentTickerData.isPriceAvailable,
                priceExchangeRate: currentTickerData.priceExchangeRate,
                theme: resolvedTheme,
                updateTheme,
            }}
        >
            {children}
        </SettingsContext.Provider>
    );
}
