import { IPaginatedResponse } from "@/types";
import { IBlock, ITransaction } from "@/types/generated";

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

export interface IHomeStatistics {
    totalSupply: number;
    voting: {
        percentage: number;
        amount: number;
    };
    blockHeight: number;
    gas: {
        low: {
            value: number;
            amount: string;
        };
        average: {
            value: number;
            amount: string;
        };
        high: {
            value: number;
            amount: string;
        };
    };
}

export interface HomeProps {
    statistics: IHomeStatistics;
    blocks?: IPaginatedResponse<IBlock>;
    transactions?: IPaginatedResponse<ITransaction>;
    baseUrl: string;
    chart: HomeChartData;
}
