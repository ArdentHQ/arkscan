import { useEffect, useRef, useState } from "react"
import dayjs, { Dayjs } from "dayjs"
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import classNames from "@/utils/class-names";
import { IValidator } from "@/types";
import { useValidatorStatus } from "@/Providers/ValidatorStatus/ValidatorStatusContext";
import { ForgingStatusGenerated, ForgingStatusGenerating, ForgingStatusMissed, ForgingStatusPending } from "@/Providers/ValidatorStatus/types";

dayjs.extend(dayjsRelativeTime);

export default function TimeToForge({
    className = 'text-theme-secondary-900 dark:text-theme-dark-50',
    // validator,
}: {
    className?: string;
    // validator: IValidator;
}) {
    // const [dateTime, setDateTime] = useState<Dayjs>(dayjs(validator.forgingAt));
    // const [output, setOutput] = useState("");
    // const [tooltip, setTooltip] = useState("");
    // const tickingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    // // const [status, setStatus] = useState<"pending" | "forging" | "forged">("pending");
    // const [justMissed, setJustMissed] = useState(wallet.justMissed);

    // useEffect(() => {
    //     setDateTime(dayjs(forgingAt));
    //     setTooltip(dateTime.format('D MMM YYYY HH:mm:ss'));

    //     if (wallet.hasForged) {
    //         setJustMissed(false);
    //         setOutput("Completed");

    //         return;
    //     }

    //     if (wallet.justMissed) {
    //         setOutput("Missed");

    //         return;
    //     }

    //     const updateOutput = () => {
    //         const now = dayjs(new Date());
    //         const seconds = dateTime.diff(now, 'second');

    //         setJustMissed(wallet.justMissed);

    //         // if (now.isAfter(dateTime)) {
    //         //     setJustMissed(true);
    //         //     setOutput(now.to(dateTime));

    //         //     return;
    //         // }

    //         if (seconds <= -2) {
    //             setJustMissed(true);
    //             setOutput(now.to(dateTime));

    //             return;
    //         }

    //         if (seconds <= 6 + secondsOffset) {
    //             setOutput('Nowwww');
    //             // setOutput(now.to(dateTime));

    //             return;
    //         }

    //         // if (seconds < 0) {
    //         //     setOutput(`~ ${Math.abs(seconds)} seconds ago`);

    //         //     return;
    //         // }

    //         if (seconds < 60) {
    //             setOutput(`~ ${seconds} seconds`);

    //             return;
    //         }

    //         setOutput(now.to(dateTime));
    //     }

    //     updateOutput();

    //     tickingTimerRef.current = setInterval(updateOutput, 100);

    //     return () => {
    //         if (! tickingTimerRef.current) {
    //             return;
    //         }

    //         clearInterval(tickingTimerRef.current);
    //     }
    // }, [forgingAt, wallet]);

    const [tooltip, setTooltip] = useState<string>();

    const { dateTime, output, status } = useValidatorStatus();

    useEffect(() => {
        setTooltip(dateTime.format('D MMM YYYY HH:mm:ss'));
    }, [dateTime]);

    return (
        <div className={classNames({
            "font-semibold !leading-4.25 text-sm": true,
            [className]: true,
        })}>
            {status === ForgingStatusGenerated && (
                <span>Completed</span>
            )}
            {status === ForgingStatusGenerating && (
                <span>Now</span>
            )}
            {status === ForgingStatusMissed && (
                <span>Missed</span>
            )}
            {status === ForgingStatusPending && (
                <Tippy content={tooltip}>
                    <div>
                        <span>{output}</span>
                        <div>-</div>
                        <div>{dateTime.format('HH:mm:ss')}</div>
                        <div>{dayjs().format('HH:mm:ss')}</div>
                    </div>
                </Tippy>
            )}

            {/* {status} */}

            {/* {wallet.hasForged && <div>Completed</div>} */}
            {/* {justMissed && <div>Missed</div>} */}
            {/* {!wallet.isPending && !wallet.hasForged && !justMissed && <div>Now</div>} */}

            {/* {dateTime.diff(dayjs(new Date()), 'second')} */}





            {/*

            {!wallet.hasForged && !wallet.justMissed && <Tippy content={tooltip}>
                <div>
                    <span>{output}</span>
                    -
                    <span>{dateTime.format('HH:mm:ss')}</span>
                </div>
            </Tippy>} */}
            {/* {wallet.isPending && <Tippy content={tooltip}>
                <div>
                    <span>{output}</span>
                    -
                    <span>{dateTime.format('HH:mm:ss')}</span>
                </div>
            </Tippy>} */}
        </div>
    )
}
