export type ITransaction = {
    hash: string;
    block_hash: string;
    block_number: number;
    transaction_index: number;
    timestamp: number;
    nonce: number;
    sender_public_key: string;
    from: string;
    to: string;
    value: string;
    gas_price: string;
    gas: string;
    status: boolean;
    gas_used: string;
    gas_refunded: string;
    deployed_contract_address: string | null;
    decoded_error: string | null;
    multi_payment_recipients: string[];
    amount: number;
    amountForItself: number;
    amountExcludingItself: number;
    amountWithFee: number;
    amountReceived: number;
    amountFiat: string | number;
    amountReceivedFiat: string | number;
    fee: number;
    feeFiat: string | number;
    type: string;
    isTransfer: boolean;
    isTokenTransfer: boolean;
    isVote: boolean;
    isUnvote: boolean;
    isValidatorRegistration: boolean;
    isValidatorResignation: boolean;
    isValidatorUpdate: boolean;
    isUsernameRegistration: boolean;
    isUsernameResignation: boolean;
    isContractDeployment: boolean;
    isMultiPayment: boolean;
    isSelfReceiving: boolean;
    isSent: boolean;
    isSentToSelf: boolean;
    isReceived: boolean;
    hasFailedStatus: boolean;
    validatorRegistration: ITransaction | null;
    votedFor: string | null;
    sender: IWallet | null;
    recipient: IWallet | null;
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
    hasUsername: boolean;
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
