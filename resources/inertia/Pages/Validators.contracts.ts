import { IFilters, ITabbedData, IValidator, IPaginatedResponse } from "@/types";
import { IBlock, IWallet, ITransaction } from "@/types/generated";
import { IForgingStats } from "@/types/generated";

export interface IValidatorData {
    statistics: IMonitorStatistics;
    overflowValidators: IValidator[];
    validators: IValidator[];
}

export interface IMonitorStatistics {
    performances?: {
        forging?: string | number;
        missed?: string | number;
        missing?: string | number;
    };
    blockCount?: number;
    nextValidator?: {
        address?: string;
        attributes?: {
            username?: string;
        };
    };
}

export interface IValidatorsStatistics {
    voterCount: number;
    totalVoted: number;
    missedBlocks: number;
    validatorsMissed: number;
    votesPercentage: number;
}

export interface ValidatorsProps {
    statistics: IValidatorsStatistics;
    validators: IPaginatedResponse<IWallet>;
    filters: ITabbedData<IFilters>;
    missedBlocks: IPaginatedResponse<IForgingStats>;
}
