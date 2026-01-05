'use client';

import { createContext, useContext } from "react";
import { MissedBlocksTrackerContextType } from "./types";

const MissedBlocksTrackerContext = createContext<MissedBlocksTrackerContextType | null>(null);

export function useMissedBlocksTracker() {
    const context = useContext(MissedBlocksTrackerContext);
    if (! context) {
        throw new Error("useMissedBlocksTracker must be used within a MissedBlocksTrackerProvider");
    }

    return context;
}

export default MissedBlocksTrackerContext;
