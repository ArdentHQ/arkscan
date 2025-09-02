export interface IBlock {
    hash: string;
    number: number;
}

export interface IWallet {
    address: string;
    username?: string;
    hasForged?: boolean;
    justMissed?: boolean;
    isResigned?: boolean;
    isPending?: boolean;
    keepsMissing?: boolean;
    missedCount?: number;
    blocksSinceLastForged?: number;
    public_key: string;
}

export interface IValidator {
    wallet: IWallet;
    lastBlock: IBlock | null;
    order: number;
    forgingAt: string | Date;
    status: 'done' | 'next' | 'pending';
    secondsUntilForge: number;
}

export interface IValidatorData {
    statistics: any;
    overflowValidators: IValidator[];
    validators: IValidator[];
}

export interface IStatistics {
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
