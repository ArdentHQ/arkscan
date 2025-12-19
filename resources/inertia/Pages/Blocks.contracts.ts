import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";

export interface IBlocksStatistics {
    forgedCount: number;
    missedCount: number;
    totalRewards: number;
    maxTransactions: number;
}

export interface BlocksListProps {
    statistics: IBlocksStatistics;
    blocks: IPaginatedResponse<IBlock>;
}
