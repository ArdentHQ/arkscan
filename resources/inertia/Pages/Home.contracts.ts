import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";

export type HomeChartPeriod = "all" | "day" | "week" | "month" | "year";

export interface HomeChartTheme {
    name: string;
    mode: string | null;
}

export interface HomeChartMarket {
    volume: string | null;
    marketCap: string | null;
}

export interface HomeChartData {
    datasets: number[];
    labels: Array<number | string>;
    theme: HomeChartTheme;
    market: HomeChartMarket;
    period: HomeChartPeriod;
    refreshInterval: number;
}

export interface HomeProps {
    transactions?: IPaginatedResponse<ITransaction>;
    baseUrl: string;
    chart?: HomeChartData;
}
