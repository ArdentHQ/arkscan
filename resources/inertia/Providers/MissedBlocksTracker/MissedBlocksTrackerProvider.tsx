'use client';

import { useEffect, useRef, useState } from "react";
import MissedBlocksTrackerContext from "./MissedBlocksTrackerContext";
import { IValidator } from "@/types";
import dayjs from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import { MissedBlocksTrackerContextType } from "./types";
import { MISSED_BLOCKS_SECONDS_THRESHOLD } from "@/constants";

dayjs.extend(dayjsRelativeTime);

export default function MissedBlocksTrackerProvider({
    validators,
    children,
}: {
    validators: IValidator[];
    children: React.ReactNode;
}) {
    const tickingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const [currentForger, setCurrentForger] = useState<IValidator | undefined>();
    const [consecutiveMissedBlocks, setConsecutiveMissedBlocks] = useState<number>(0);
    const [secondsOffset, setSecondsOffset] = useState<number>(0);

    useEffect(() => {
        const updateCurrentForger = () => {
            const sortedValidators = [...validators];
            sortedValidators.sort((a, b) => dayjs(a.forgingAt).unix() - dayjs(b.forgingAt).unix());

            const now = dayjs(new Date());

            const forger = sortedValidators.filter(validator => {
                const secondsDifference = dayjs(validator.forgingAt).diff(now, 'second');
                if (secondsDifference < MISSED_BLOCKS_SECONDS_THRESHOLD) {
                    return false;
                }

                if (validator.wallet.hasForged) {
                    return false
                }

                if (validator.wallet.justMissed) {
                    return false;
                }

                return true;
            }).shift();

            setCurrentForger(forger);

            if (! forger) {
                setConsecutiveMissedBlocks(0);

                return;
            }

            const missedBlocks = sortedValidators.filter(validator => validator.order < forger?.order);
            missedBlocks.sort((a, b) => b.order - a.order);

            let missedBlocksCount = 0;
            let calculatedSecondsOffset = 0;
            for (const validator of missedBlocks) {
                if (validator.wallet.hasForged) {
                    break;
                }

                const secondsDifference = dayjs(validator.forgingAt).diff(now, 'second');
                if (secondsDifference >= MISSED_BLOCKS_SECONDS_THRESHOLD) {
                    break;
                }

                missedBlocksCount++;
                calculatedSecondsOffset += missedBlocksCount * 2;
            }

            if (missedBlocksCount !== consecutiveMissedBlocks) {
                setConsecutiveMissedBlocks(missedBlocksCount);
            }

            if (calculatedSecondsOffset !== secondsOffset) {
                setSecondsOffset(calculatedSecondsOffset);
            }
        }

        tickingTimerRef.current = setInterval(updateCurrentForger, 100);

        return () => {
            if (! tickingTimerRef.current) {
                return;
            }

            clearInterval(tickingTimerRef.current);
        }
    }, [validators]);

    const value: MissedBlocksTrackerContextType = {
        consecutiveMissedBlocks,
        currentForger,
        secondsOffset,
    };

    return (
        <MissedBlocksTrackerContext.Provider value={value}>
            {children}
        </MissedBlocksTrackerContext.Provider>
    );
};
