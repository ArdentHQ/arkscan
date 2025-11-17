import { createContext } from "react";

export interface ISettingsContext {
    currency: string;
    updateCurrency: (newCurrency: string) => Promise<void>;
    isUpdatingCurrency: boolean;
    isPriceAvailable: boolean;
    priceExchangeRate: number | null;
}

const SettingsContext = createContext<ISettingsContext | null>(null);

export default SettingsContext;
