import { createContext } from "react";

export interface ICurrencyContext {
    currency: string;
}

const CurrencyContext = createContext<ICurrencyContext | null>(null);

export default CurrencyContext;
