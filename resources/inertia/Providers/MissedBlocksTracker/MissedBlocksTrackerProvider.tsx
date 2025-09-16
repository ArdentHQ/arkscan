'use client';

import { useEffect, useRef, useState } from "react";
import MissedBlocksTrackerContext from "./MissedBlocksTrackerContext";
import { IValidator } from '../../types';
import dayjs, { Dayjs } from "dayjs";
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
    // const [lastForged, setLastForged] = useState<IValidator | undefined>();
    const [consecutiveMissedBlocks, setConsecutiveMissedBlocks] = useState<number>(0);
    const [secondsOffset, setSecondsOffset] = useState<number>(0);

    useEffect(() => {
        const updateCurrentForger = () => {
            const sortedValidators = [...validators].map(validator => {
                return {
                    ...validator,

                    forgingAt: dayjs(validator.forgingAt),
                } as IValidator & { forgingAt: Dayjs };
            });

            // sortedValidators.sort((a, b) => a.order - b.order);

            // const sortedValidators = [...validators];

            sortedValidators.sort((a, b) => a.forgingAt.unix() - b.forgingAt.unix());

            const now = dayjs(new Date());

            const forger = sortedValidators.filter(validator => {
                const secondsDifference = validator.forgingAt.diff(now, 'second');
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

            const missedBlocks = sortedValidators.filter(validator => validator.forgingAt.unix() < forger?.forgingAt.unix());
            missedBlocks.sort((a, b) => b.order - a.order);

            let missedBlocksCount = 0;
            let calculatedSecondsOffset = 0;
            for (const validator of missedBlocks) {
                if (validator.wallet.hasForged) {
                    // setLastForged(validator);

                    break;
                }

                const threshold = MISSED_BLOCKS_SECONDS_THRESHOLD + calculatedSecondsOffset;
                const secondsDifference = dayjs(validator.forgingAt).diff(now, 'second');
                if (secondsDifference >= threshold) {
                    break;
                }

                missedBlocksCount++;
                // calculatedSecondsOffset += 2;
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

    const calculateSecondsOffset = (validator: IValidator) => {
        if (consecutiveMissedBlocks === 0) {
            return 0;
        }

        if (consecutiveMissedBlocks === 1) {
            return 2;
        }

        let calculatedOffset = secondsOffset;
        const sortedValidators = [...validators].map(validator => {
            return {
                ...validator,

                forgingAt: dayjs(validator.forgingAt).unix(),
            } as IValidator & { forgingAt: number };
        });

        const missedBlocks = sortedValidators.filter(v => v.forgingAt < dayjs(validator.forgingAt).unix());
        missedBlocks.sort((a, b) => b.forgingAt - a.forgingAt);

        let missedIndex: number = 0;
        for (const v of missedBlocks) {
            if (v.wallet.hasForged) {
                break;
            }

            if (v.wallet.address === validator.wallet.address) {
                break;
            }

            calculatedOffset -= missedIndex * 2;
            missedIndex++;
        }

        return calculatedOffset;
    }

    useEffect(() => {
        console.log({consecutiveMissedBlocks, currentForger, secondsOffset});
    }, [consecutiveMissedBlocks, currentForger, secondsOffset]);

    const value: MissedBlocksTrackerContextType = {
        consecutiveMissedBlocks,
        currentForger,
        secondsOffset,
        calculateSecondsOffset,
    };

    return (
        <MissedBlocksTrackerContext.Provider value={value}>
            {children}
        </MissedBlocksTrackerContext.Provider>
    );
};
