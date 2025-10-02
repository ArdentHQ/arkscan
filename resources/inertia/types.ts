export interface IBlock {
    hash: string;
    number: number;
}

export interface IWallet {
    address: string;
    username?: string;
    hasForged?: boolean;
    justMissed?: boolean;
    isResigned?: boolean;
    isPending?: boolean;
    keepsMissing?: boolean;
    missedCount?: number;
    blocksSinceLastForged?: number;
    durationSinceLastForged?: number;
    public_key: string;

    isCold: boolean;
    isLegacy: boolean;
    isDormant: boolean;
    isValidator: boolean;
    isActive: boolean;
    productivity: number;
    legacyAddress: string | null;
    vote: IWallet | null;
    attributes: Record<string, any> | null;
    votes: string;

    balance: string;
    formattedBalanceTwoDecimals: string;
    formattedBalanceFull: string;
    fiatValue: number | string;
    totalForged: string;
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
    epoch: string;
    validatorCount: number;
    blockTime: number;
    blockReward: number;
    supply: number;
}

export interface ISettings {
    currency: string;
    priceChart: boolean;
    feeChart: boolean;
    theme: string;
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

export type Currencies = Record<string, ICurrency>;
