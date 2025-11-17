import { createContext } from "react";

export interface ISettingsContext {
    currency: string;
    updateCurrency: (newCurrency: string) => Promise<void>;
    isUpdatingCurrency: boolean;
    isPriceAvailable: boolean;
    priceExchangeRate: number | null;
    theme: string;
    updateTheme: (newTheme: string) => Promise<void>;
    isUpdatingTheme: boolean;
}

const SettingsContext = createContext<ISettingsContext | null>(null);

export default SettingsContext;
