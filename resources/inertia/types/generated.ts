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
export type IRequestData = {
    currencies: Record<string, ICurrency>;
    network: Array<any>;
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
