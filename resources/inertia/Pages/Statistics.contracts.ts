export type StatisticsPeriod = "day" | "week" | "month" | "quarter" | "year" | "all";

export interface StatisticsChartTheme {
    name: string;
    mode: string | null;
}

export interface StatisticsChartData {
    labels: Array<number | string>;
    datasets: number[];
    theme: StatisticsChartTheme;
}

export interface GasTrackerFee {
    amount: string;
    duration: number;
    durationLabel: string;
    value: number | null;
}

export interface GasTrackerData {
    canBeExchanged: boolean;
    fees: {
        low: GasTrackerFee;
        average: GasTrackerFee;
        high: GasTrackerFee;
    };
}

export interface HighlightsData {
    totalSupply: number;
    voting: {
        percentage: number;
        value: number;
    };
    validators: number;
    wallets: number;
}

export interface InformationCardPeriodData {
    value: number;
    chart: StatisticsChartData;
    aboveThreshold?: boolean;
}

export interface InformationCardData {
    allTimeValue: number;
    periods: Record<StatisticsPeriod, InformationCardPeriodData>;
}

export interface InformationCardsData {
    refreshInterval: number;
    defaultPeriod: StatisticsPeriod;
    periods: StatisticsPeriod[];
    transactions: InformationCardData;
    fees: InformationCardData;
}

export interface StatisticsTransactionRecord {
    type: "transaction";
    url: string;
    hash: string;
    amount: number;
    timestamp: number;
}

export interface StatisticsBlockRecord {
    type: "block";
    url: string;
    height: number;
    timestamp: number;
    fee?: number;
    transactionCount?: number;
}

export type StatisticsRecord = StatisticsTransactionRecord | StatisticsBlockRecord;

export interface StatisticsTransactionInsights {
    details: Record<string, number>;
    averages: {
        transactions: number;
        transaction_volume: number;
        transaction_fees: number;
    };
    records: Record<string, StatisticsRecord | null>;
}

export interface StatisticsMarketDataInsights {
    prices: {
        daily: { low: number | null; high: number | null };
        year: { low: number | null; high: number | null };
        atl: { value: number | null; timestamp: number | null };
        ath: { value: number | null; timestamp: number | null };
    };
    volume: {
        today: number;
        atl: { value: number; timestamp: number | null };
        ath: { value: number; timestamp: number | null };
    };
    caps: {
        today: number | null;
        atl: { value: number | null; timestamp: number | null };
        ath: { value: number | null; timestamp: number | null };
    };
}

export interface WalletSummary {
    address: string;
    username: string | null;
    hasUsername: boolean;
    url: string;
}

export interface StatisticsValidatorRow {
    key: string;
    wallet: WalletSummary | null;
    value: string | number | null;
}

export interface StatisticsAddressHoldingsRow {
    grouped: number;
    count: number;
}

export interface StatisticsUniqueAddressRow {
    address: string;
    value: string | number;
}

export type StatisticsLargestAddressRow = StatisticsUniqueAddressRow;

export interface StatisticsUniqueAddresses {
    genesis: StatisticsUniqueAddressRow | null;
    newest: StatisticsUniqueAddressRow | null;
    most_transactions: StatisticsUniqueAddressRow | null;
    largest: StatisticsLargestAddressRow | null;
}

export interface StatisticsAddressInsights {
    holdings: StatisticsAddressHoldingsRow[];
    unique: StatisticsUniqueAddresses;
}

export interface StatisticsAnnualRow {
    year: number;
    transactions: number;
    volume: number;
    fees: number;
    blocks: number;
}

export interface StatisticsInsights {
    transactions: StatisticsTransactionInsights;
    marketData: StatisticsMarketDataInsights | null;
    validators: StatisticsValidatorRow[];
    addresses: StatisticsAddressInsights;
    annual: StatisticsAnnualRow[];
}

export interface StatisticsProps {
    refreshInterval: number;
    snapshotBlockHeight: number;
    gasTracker: GasTrackerData;
    highlights: HighlightsData;
    informationCards: InformationCardsData;
    insights: StatisticsInsights;
}
