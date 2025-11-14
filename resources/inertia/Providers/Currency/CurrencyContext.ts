import { createContext } from "react";

export interface ICurrencyContext {
    currency: string;
    updateCurrency: (newCurrency: string) => Promise<void>;
    isUpdatingCurrency: boolean;
}

const CurrencyContext = createContext<ICurrencyContext | null>(null);

export default CurrencyContext;
