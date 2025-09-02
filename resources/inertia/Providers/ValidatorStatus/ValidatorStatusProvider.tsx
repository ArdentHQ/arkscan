'use client';

import { useEffect, useRef, useState } from "react";
import ValidatorStatusContext from "./ValidatorStatusContext";
import { IValidator } from '../../types';
import dayjs, { Dayjs } from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import { ForgingStatus, ForgingStatusGenerated, ForgingStatusGenerating, ForgingStatusMissed, ForgingStatusPending, ValidatorStatusContextType } from "./types";
import { useMissedBlocksTracker } from "../MissedBlocksTracker/MissedBlocksTrackerContext";

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

    const { secondsOffset, currentForger } = useMissedBlocksTracker();

    useEffect(() => {
        setDateTime(dayjs(forgingAt));

        if (validator.wallet.hasForged) {
            setStatus(ForgingStatusGenerated);

            return;
        }

        if (validator.wallet.justMissed) {
            setStatus(ForgingStatusMissed);

            return;
        }

        const updateOutput = () => {
            const now = dayjs(new Date());
            const seconds = dateTime.diff(now, 'second');

            if (currentForger?.wallet.address !== validator.wallet.address && seconds <= 0 - secondsOffset) {
                setStatus(ForgingStatusMissed);

                // setJustMissed(true);
                // setOutput(now.to(dateTime));

                return;
            }

            if (currentForger?.wallet.address === validator.wallet.address) {
                setStatus(ForgingStatusGenerating);

                // setOutput('Nowwww');
                // setOutput(now.to(dateTime));

                return;
            }

            if (seconds < 60) {
                setOutput(`~ ${seconds} seconds`);

                return;
            }

            setOutput(now.to(dateTime));
        }

        updateOutput();

        tickingTimerRef.current = setInterval(updateOutput, 100);

        return () => {
            if (! tickingTimerRef.current) {
                return;
            }

            clearInterval(tickingTimerRef.current);
        }
    }, [forgingAt, validator.wallet]);

    const value: ValidatorStatusContextType = {
        output,
        dateTime,
        status,
        validator,
    };

    return (
        <ValidatorStatusContext.Provider value={value}>
            {children}
        </ValidatorStatusContext.Provider>
    );
};
