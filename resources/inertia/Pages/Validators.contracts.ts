import { IFilters, ITabbedData, IMonitorValidator, IPaginatedResponse } from "@/types";
import { ITransaction, IValidator } from "@/types/generated";
import { IForgingStats } from "@/types/generated";

export interface IValidatorData {
    statistics: IMonitorStatistics;
    overflowValidators: IMonitorValidator[];
    validators: IMonitorValidator[];
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
    validators: IPaginatedResponse<IValidator>;
    filters: {
        validators: {
            active: boolean;
            standby: boolean;
            dormant: boolean;
            resigned: boolean;
        };
        recentVotes: {
            vote: boolean;
            unvote: boolean;
        };
    };
    recentVotes: IPaginatedResponse<ITransaction>;
    missedBlocks: IPaginatedResponse<IForgingStats>;
}
