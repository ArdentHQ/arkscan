import { IFilters, ITabbedData, IValidator, IMonitorValidator, IPaginatedResponse } from "@/types";
import { IValidator } from "@/types/generated";
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
    filters: ITabbedData<IFilters>;
    missedBlocks: IPaginatedResponse<IForgingStats>;
}
