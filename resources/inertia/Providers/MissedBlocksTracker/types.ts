import { IValidator } from "@/types";

export interface MissedBlocksTrackerContextType {
    consecutiveMissedBlocks: number;
    currentForger?: IValidator;
    secondsOffset?: number;
    calculateSecondsOffset: (currentForger: IValidator) => number;
}
