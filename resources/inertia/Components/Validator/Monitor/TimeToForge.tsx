import { useEffect, useRef, useState } from "react"
import dayjs, { Dayjs } from "dayjs"
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import classNames from "@/utils/class-names";
import { IWallet } from "@/types";

dayjs.extend(dayjsRelativeTime);

export default function TimeToForge({
    forgingAt,
    wallet,
    className = 'text-theme-secondary-900 dark:text-theme-dark-50',
}: {
    forgingAt: string | Date;
    wallet: IWallet;
    className?: string;
}) {
    const [dateTime, setDateTime] = useState<Dayjs>(dayjs(forgingAt));
    const [output, setOutput] = useState("");
    const [tooltip, setTooltip] = useState("");
    const tickingTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        setDateTime(dayjs(forgingAt));
        setTooltip(dateTime.format('D MMM YYYY HH:mm:ss'));

        const updateOutput = () => {
            const now = dayjs(new Date());
            const seconds = dateTime.diff(now, 'second');

            if (seconds < -60) {
                setOutput(now.to(dateTime));

                return;
            }

            if (seconds < 0) {
                setOutput(`~ ${Math.abs(seconds)} seconds ago`);

                return;
            }

            if (seconds < 60) {
                setOutput(`~ ${seconds} seconds`);

                return;
            }

            setOutput(now.to(dateTime));
        }

        updateOutput();

        tickingTimerRef.current = setInterval(updateOutput, 1000);

        return () => {
            if (! tickingTimerRef.current) {
                return;
            }

            clearInterval(tickingTimerRef.current);
        }
    }, [forgingAt]);

    return (
        <div className={classNames({
            "font-semibold !leading-4.25 text-sm": true,
            [className]: true,
        })}>
            {wallet.hasForged && <div>Completed</div>}
            {!wallet.isPending && !wallet.hasForged && !wallet.justMissed && <div>Now</div>}
            {wallet.justMissed && <div>Missed</div>}
            {wallet.isPending && <Tippy content={tooltip}>
                <span>{output}</span>
            </Tippy>}
        </div>
    )
}
