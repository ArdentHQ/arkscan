import { IExchange } from "../types/generated";
import { IPaginatedResponse } from "../types";

export interface ExchangeDropdownItem {
    title: string;
    value: string;
}

export type ExchangeChartPeriod = "day" | "week" | "month" | "quarter" | "year" | "all";

export interface ExchangeChartTheme {
    name: string;
    mode: string | null;
}

export interface ExchangeChartOption {
    value: ExchangeChartPeriod;
    label: string;
}

export interface ExchangeChartData {
    datasets: number[];
    labels: Array<number | string>;
    theme: ExchangeChartTheme;
    period: ExchangeChartPeriod;
    options: ExchangeChartOption[];
    refreshInterval: number;
    mainValueFiat: string;
    mainValuePercentage: number;
    mainValueVariation: "red" | "green";
    marketCapValue: string | null;
    minPriceValue: string;
    maxPriceValue: string;
    dateUnitOverride?: string | null;
}

export interface ExchangesProps {
    typeOptions: ExchangeDropdownItem[];
    pairOptions: ExchangeDropdownItem[];
    exchanges: IPaginatedResponse<IExchange>;
    chart?: ExchangeChartData;
}
