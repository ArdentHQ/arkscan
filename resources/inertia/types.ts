import { IBlock, IWallet } from "./types/generated";

export type NavigationItem = {
    label: string;
    route?: string;
    children?: NavigationItem[];
};

export type Navigation = NavigationItem[];

export interface IValidator {
    wallet: IWallet;
    lastBlock: IBlock | null;
    order: number;
    forgingAt: string | Date;
    status: "done" | "next" | "pending";
    secondsUntilForge: number;
}

export interface IValidatorData {
    statistics: any;
    overflowValidators: IValidator[];
    validators: IValidator[];
}

export interface IStatistics {
    performances?: {
        forging?: string | number;
        missed?: string | number;
        missing?: string | number;
    };
    blockCount?: number;
    nextValidator?: {
        address?: string;
        attributes?: {
            username?: string;
        };
    };
}

export interface IPaginatedResponse<T> {
    data: T[];
    current_page: number;
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    meta: {
        pageName: string;
        urlParams: Record<string, any>;
    };
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;

    noResultsMessage: string;
}

export interface ITabbedData<T> {
    [tab: string]: T;
}

export interface IFilters {
    [key: string]: boolean;
}
