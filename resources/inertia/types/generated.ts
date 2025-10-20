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
