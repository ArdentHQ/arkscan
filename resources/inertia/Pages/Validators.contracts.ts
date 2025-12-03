export interface IStatistics {
    voterCount: number;
    totalVoted: number;
    missedBlocks: number;
    validatorsMissed: number;
    votesPercentage: number;
}

export interface ValidatorsProps {
    statistics: IStatistics;
}
