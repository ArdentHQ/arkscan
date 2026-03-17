export type IBlock = {
    hash: string;
    number: number;
    timestamp: number;
    transactionCount: number;
    reward: number;
    fee: number;
    exchangeRates: Record<string, number>;
    confirmations: number;
    proposer: IMemoryWallet;
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
export type IExchange = {
    id: number;
    name: string;
    url: string;
    is_exchange: boolean;
    is_aggregator: boolean;
    btc: boolean;
    eth: boolean;
    stablecoins: boolean;
    other: boolean;
    icon: string;
    iconUrl: string;
    coingecko_id: string | null;
    price: number | null;
    priceFiat: number | null;
    volume: number | null;
    volumeFiat: number | null;
};
export type IForgingStats = {
    number: number;
    timestamp: number;
    validator: IWallet | null;
    voterCount: number | null;
    votesPercentage: number | null;
    votes: number | null;
};
export type IMemoryWallet = {
    address: string;
    publicKey: string | null;
    isContract: boolean;
    hasUsername: boolean;
    username: string | null;
    isValidator: boolean;
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
    sender: INavbarSearchMemoryWallet;
    recipient: INavbarSearchMemoryWallet;
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
        approve: string;
        contract_deployment: string;
        batch_transfer: string;
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
    usesBroadcasting: boolean;
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
    currentRoute: string | null;
    contactEmail: string;
    urls: { arkvault: string; arkconnect: string };
};
export type ISettings = {
    currency: string;
    priceChart: boolean;
    feeChart: boolean;
    theme: string | null;
};
export type IToken = {
    address: string;
    name: string;
    symbol: string;
    symbolFull: string;
    decimals: number;
    totalSupply: string;
    deploymentHash: string;
};
export type ITokenHolder = {
    wallet: IMemoryWallet;
    token: IToken;
    balance: number;
};
export type ITokenTransfer = {
    transaction_hash: string;
    from: IMemoryWallet;
    to: IMemoryWallet;
    amount: number;
    value: string;
    block_number: number;
    index: number;
    token: IToken;
    transaction: ITransaction;
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
    multiPaymentRecipients: { address: string; amount: string }[];
    exchangeRates: Record<string, number>;
    url: string;
    methodData: { functionName: string | null; methodId: string | null; arguments: string[] | null };
    tokenApprovalDetails: {
        spender: IWalletReference;
        amount: string | null;
        isUnlimited: boolean;
        isRevoke: boolean;
    } | null;
    validatorRegistration: ITransaction | null;
    votedFor: string | null;
    votedForUsername: string | null;
    sender: IWalletReference | null;
    recipient: IWalletReference | null;
};
export type ITransactionDetails = {
    confirmations: number;
    transactionError: string | null;
    recipientIsContract: boolean;
    validatorPublicKey: string | null;
    username: string | null;
    tokenTransfer: { recipient: IWalletReference; amount: string | null } | null;
    tokenApproval: { spender: IWalletReference; amount: string | null; isUnlimited: boolean; isRevoke: boolean } | null;
    token: IToken | null;
    payload: { formatted: string | null; utf8: string | null; raw: string | null } | null;
    batchTokenTransfers: { recipient: IWalletReference; amount: string }[];
};
export type IValidator = {
    rank: number | null;
    address: string;
    isActive: boolean;
    isDormant: boolean;
    isResigned: boolean;
    username: string | null;
    hasUsername: boolean;
    voterCount: number;
    votes: number;
    votesPercentage: number;
    missedBlocks: number;
    missedBlocksState: "success" | "warning" | "danger" | "inactive";
    voteUrl: string | null;
};
export type IWallet = {
    address: string;
    balance: string;
    nonce: string;
    public_key: string | null;
    legacyAddress: string | null;
    username: string | null;
    votes: string;
    productivity: number;
    balancePercentage: number;
    totalForged: string;
    attributes: Record<string, any>;
    vote: IWallet | null;
    voteUrl: string | null;
    votePercentage: number | null;
};
export type IWalletReference = {
    address: string;
    username: string | null;
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
