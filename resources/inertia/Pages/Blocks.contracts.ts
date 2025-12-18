export interface IBlocksStatistics {
    forgedCount: number;
    missedCount: number;
    totalRewards: number;
    maxTransactions: number;
}

export interface BlocksListProps {
    statistics: IBlocksStatistics;
}
