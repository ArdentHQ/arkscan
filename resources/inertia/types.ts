import { IWallet } from "./types/generated";

export interface IBlock {
    hash: string;
    number: number;
}

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

export interface INetwork {
    coin: string;
    name: string;
    alias: string;
    api: string;
    explorerTitle: string;
    legacyExplorerUrl: string;
    currency: string;
    currencySymbol: string;
    confirmations: number;
    knownWalletsUrl: string;
    knownWallets: string[];
    knownContracts: string[];
    canBeExchanged: boolean;
    nethash: string;
    epoch: string;
    validatorCount: number;
    blockTime: number;
    blockReward: number;
    supply: number;
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

export interface ISettings {
    currency: string;
    priceChart: boolean;
    feeChart: boolean;
    theme: string;
}

export interface IConfigPagination {
    per_page: number;
}

export interface ICurrency {
    currency: string;
    locale: string | null;
    symbol: string | null;
}

export interface IConfigProductivity {
    warning: number;
    danger: number;
}

export interface IConfigArkConnect {
    enabled: boolean;
    vaultUrl: string;
}

export type Currencies = Record<string, ICurrency>;
