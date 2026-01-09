import { IPaginatedResponse } from "@/types";
import { IBlock, ITransaction } from "@/types/generated";

export interface IHomeStatistics {
    totalSupply: number;
    voting: {
        percentage: number;
        amount: number;
    };
    addresses: number;
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
}
