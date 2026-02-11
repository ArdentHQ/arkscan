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
    value: string | null;
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
    totalSupply: string;
    voting: {
        percentage: string;
        value: string;
    };
    validators: string;
    wallets: string;
}

export interface InformationCardPeriodData {
    value: string;
    chart: StatisticsChartData;
    tooltip?: string | null;
}

export interface InformationCardData {
    allTimeValue: string;
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
    amount: string;
    date: string;
}

export interface StatisticsBlockRecord {
    type: "block";
    url: string;
    height: number;
    date: string;
    fee?: string;
    transactionCount?: number;
}

export type StatisticsRecord = StatisticsTransactionRecord | StatisticsBlockRecord;

export interface StatisticsTransactionInsights {
    details: Record<string, number>;
    averages: {
        transactions: number;
        transaction_volume: string;
        transaction_fees: string;
    };
    records: Record<string, StatisticsRecord | null>;
}

export interface StatisticsMarketDataInsights {
    prices: {
        daily: { low: string | null; high: string | null };
        year: { low: string | null; high: string | null };
        atl: { value: string | null; date: string | null };
        ath: { value: string | null; date: string | null };
    };
    volume: {
        today: string;
        atl: { value: string; date: string | null };
        ath: { value: string; date: string | null };
    };
    caps: {
        today: string | null;
        atl: { value: string | null; date: string | null };
        ath: { value: string | null; date: string | null };
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

export interface StatisticsLargestAddressRow extends StatisticsUniqueAddressRow {
    valueShort: string;
    valueFull: string;
}

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
    volume: string;
    fees: string;
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
