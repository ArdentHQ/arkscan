import { useEffect, useState } from "react"
import dayjs from "dayjs"
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import Tippy from '@tippyjs/react';
import 'tippy.js/dist/tippy.css';
import classNames from "@/utils/class-names";
import { useValidatorStatus } from "@/Providers/ValidatorStatus/ValidatorStatusContext";
import { ForgingStatusGenerated, ForgingStatusGenerating, ForgingStatusMissed } from "@/Providers/ValidatorStatus/types";

dayjs.extend(dayjsRelativeTime);

export default function TimeToForge({ className = 'text-theme-secondary-900 dark:text-theme-dark-50' }: { className?: string }) {
    const [tooltip, setTooltip] = useState<string>();

    const { dateTime, output, status, seconds } = useValidatorStatus();

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
            {! [ForgingStatusGenerated, ForgingStatusGenerating, ForgingStatusMissed].includes(status) && (
                <Tippy content={tooltip}>
                    <div>
                        <span>{output}</span>
                        <div>-</div>
                        <div>{dateTime.format('HH:mm:ss')}</div>
                        <div>{dayjs().format('HH:mm:ss')}</div>
                    </div>
                </Tippy>
            )}

            <span>{seconds}</span>
        </div>
    )
}
