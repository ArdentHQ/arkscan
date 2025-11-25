export type IBlock = {
    hash: string;
    number: number;
    timestamp: number;
    transactionCount: number;
    totalReward: number;
    totalRewardFiat: string;
    rewardFiat: string;
};
export type IConfigArkconnect = {
    enabled: boolean;
    vaultUrl: string;
};
export type IConfigPagination = {
    per_page: number;
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
export type INavbarSearchBlockResultData = {
    hash: string;
    transactionCount: number;
    validator: INavbarSearchMemoryWallet | null;
};
export type INavbarSearchMemoryWallet = {
    address: string | null;
    username: string | null;
    isContract: boolean;
};
export type INavbarSearchTransactionResultData = {
    hash: string;
    amountWithFee: number;
    isVote: boolean;
    isUnvote: boolean;
    isTransfer: boolean;
    isTokenTransfer: boolean;
    sender: INavbarSearchMemoryWallet | null;
    recipient: INavbarSearchMemoryWallet | null;
    typeName: string;
    votedValidatorLabel: string | null;
};
export type INavbarSearchWalletResultData = {
    address: string;
    username: string | null;
    hasUsername: boolean;
    isKnown: boolean;
    balance: number;
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
export type IPriceTickerData = {
    currency: string;
    isPriceAvailable: boolean;
    priceExchangeRate: number | null;
};
export type IRequestData = {
    currencies: Record<string, ICurrency>;
    network: INetwork;
    productivity: IConfigProductivity;
    settings: ISettings;
    arkconnectConfig: IConfigArkconnect;
    pagination: IConfigPagination;
    broadcasting: string;
    networkName: string;
    isDownForMaintenance: boolean;
    isProduction: boolean;
    priceTickerData: IPriceTickerData;
    theme: string;
    mainnetExplorerUrl: string;
    testnetExplorerUrl: string;
    supportEnabled: boolean;
    navbarTag: string | null;
    navbarName: string | null;
};
export type ISettings = {
    currency: string;
    priceChart: boolean;
    feeChart: boolean;
    theme: string | null;
};
export type ITransaction = {
    hash: string;
    block_hash: string;
    block_number: number;
    transaction_index: number;
    timestamp: number;
    nonce: number;
    sender_public_key: string;
    from: string;
    to: string | null;
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
    url: string;
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
    voteUrl: string | null;
    votePercentage: number | null;
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
