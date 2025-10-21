export type IConfigArkconnect = {
    enabled: boolean;
    vaultUrl: string;
};
export type IConfigProductivity = {
    danger: number;
    warning: number;
};
export type ICurrency = {
    currency: string;
    locale: string | null;
    symbol: string | null;
};
export type INetwork = {
    coin: string;
    name: string;
    api: string;
    alias: string;
    nethash: string;
    mainnetExplorerUrl: string;
    testnetExplorerUrl: string;
    legacyExplorerUrl: string;
    currency: string;
    currencySymbol: string;
    confirmations: number;
    knownWallets: Array<any>;
    knownWalletsUrl: string;
    canBeExchanged: boolean;
    epoch: string;
    validatorCount: number;
    blockTime: number;
    blockReward: number;
    base58Prefix: number;
    contractAddresses: {
        consensus: string;
        multipayment: string;
        username: string;
    };
    contractMethods: {
        transfer: string;
        multipayment: string;
        vote: string;
        unvote: string;
        validator_registration: string;
        validator_resignation: string;
        validator_update: string;
        username_registration: string;
        username_resignation: string;
        contract_deployment: string;
    };
};
export type IRequestData = {
    currencies: Record<string, ICurrency>;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    arkconnect: IConfigArkconnect;
};
export type ISettings = {
    currency: string;
    priceChart: boolean;
    feeChart: boolean;
    theme: string;
};
export type IWallet = {
    address: string;
    balance: string;
    nonce: string;
    public_key: string | null;
    isActive: boolean;
    isCold: boolean;
    isValidator: boolean;
    isLegacy: boolean;
    isDormant: boolean;
    isResigned: boolean;
    legacyAddress: string | null;
    username: string | null;
    votes: string;
    productivity: number;
    formattedBalanceTwoDecimals: string;
    formattedBalanceFull: string;
    fiatValue: string;
    totalForged: string;
    attributes: Record<string, any>;
    vote: IWallet | null;
};
export enum SortDirection {
    ASC = "asc",
    DESC = "desc",
}
export enum WebhookEvents {
    BlockApplied = "block.applied",
    TransactionApplied = "transaction.applied",
    WalletVote = "wallet.vote",
}
