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
    status: 'done' | 'next' | 'pending';
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
