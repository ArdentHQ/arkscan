'use client';

import { useEffect, useRef, useState } from "react";
import ValidatorStatusContext from "./ValidatorStatusContext";
import { IValidator } from '../../types';
import dayjs, { Dayjs } from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import { ForgingStatus, ForgingStatusGenerated, ForgingStatusGenerating, ForgingStatusMissed, ForgingStatusPending, IValidatorStatusContextType } from "./types";
import { useMissedBlocksTracker } from "../MissedBlocksTracker/MissedBlocksTrackerContext";
import { MISSED_BLOCKS_SECONDS_THRESHOLD } from "@/constants";

dayjs.extend(dayjsRelativeTime);

export default function ValidatorStatusProvider({
    forgingAt,
    validator,
    children,
}: {
    forgingAt: string | Date;
    validator: IValidator;
    children: React.ReactNode;
}) {
    const [dateTime, setDateTime] = useState<Dayjs>(dayjs(forgingAt));
    const [output, setOutput] = useState("");
    const tickingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const [status, setStatus] = useState<ForgingStatus>(ForgingStatusPending);
    const [seconds, setSeconds] = useState(0);

    const { secondsOffset, currentForger } = useMissedBlocksTracker();

    // const [secondsOffset, setSecondsOffset] = useState(0);
    // const { calculateSecondsOffset, currentForger } = useMissedBlocksTracker();
    // useEffect(() => {
    //     const offset = calculateSecondsOffset(validator);

    //     setSecondsOffset(offset);
    // }, [validator.forgingAt]);

    useEffect(() => {
        if (validator.wallet.hasForged) {
            return;
        }

        if (validator.wallet.justMissed) {
            return;
        }

        setDateTime(dayjs(forgingAt));

        const updateSeconds = () => {
            const now = dayjs(new Date());
            const forgingAtDateTime = dateTime.clone().add(secondsOffset ?? 0, 'second');
            const secondsDifference = forgingAtDateTime.diff(now, 'second');

            setSeconds(secondsDifference);

            if (secondsDifference < 60) {
                setOutput(`~ ${secondsDifference} seconds`);

                return;
            }

            setOutput(now.to(forgingAtDateTime));
        }

        updateSeconds();

        tickingTimerRef.current = setInterval(updateSeconds, 100);

        return () => {
            if (! tickingTimerRef.current) {
                return;
            }

            clearInterval(tickingTimerRef.current);
        }
    }, [forgingAt, validator.wallet.hasForged, validator.wallet.justMissed, secondsOffset]);

    useEffect(() => {
        if (validator.wallet.hasForged) {
            setStatus(ForgingStatusGenerated);

            return;
        }

        if (validator.wallet.justMissed) {
            setStatus(ForgingStatusMissed);

            return;
        }

        if (currentForger?.wallet.address !== validator.wallet.address && seconds >= 60) {
            setStatus(ForgingStatusPending);

            return;
        }

        if (currentForger?.wallet.address !== validator.wallet.address && seconds <= MISSED_BLOCKS_SECONDS_THRESHOLD + secondsOffset) {
            console.log('secondsOffset', secondsOffset);
            setStatus(ForgingStatusMissed);

            return;
        }

        if (currentForger?.wallet.address === validator.wallet.address) {
            setStatus(ForgingStatusGenerating);

            return;
        }

        if (status === ForgingStatusGenerating && currentForger?.wallet.address !== validator.wallet.address) {
            setStatus(ForgingStatusMissed);

            return;
        }
    }, [validator.wallet, currentForger, seconds, status]);

    const value: IValidatorStatusContextType = {
        output,
        dateTime,
        status,
        validator,
        seconds,
    };

    return (
        <ValidatorStatusContext.Provider value={value}>
            {children}
        </ValidatorStatusContext.Provider>
    );
};
