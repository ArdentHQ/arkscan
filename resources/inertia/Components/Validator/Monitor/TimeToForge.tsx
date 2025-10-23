import { useEffect, useState } from "react";
import dayjs from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import classNames from "@/utils/class-names";
import { useValidatorStatus } from "@/Providers/ValidatorStatus/ValidatorStatusContext";
import {
    ForgingStatusGenerated,
    ForgingStatusGenerating,
    ForgingStatusMissed,
} from "@/Providers/ValidatorStatus/types";
import Tooltip from "@/Components/General/Tooltip";

dayjs.extend(dayjsRelativeTime);

export default function TimeToForge({
    className = "text-theme-secondary-900 dark:text-theme-dark-50",
}: {
    className?: string;
}) {
    const [tooltip, setTooltip] = useState<string>();

    const { dateTime, output, status } = useValidatorStatus();

    useEffect(() => {
        setTooltip(dateTime.format("D MMM YYYY HH:mm:ss"));
    }, [dateTime]);

    return (
        <div
            className={classNames({
                "text-sm font-semibold !leading-4.25": true,
                [className]: true,
            })}
        >
            {status === ForgingStatusGenerated && <span>Completed</span>}

            {status === ForgingStatusGenerating && <span>Now</span>}

            {status === ForgingStatusMissed && <span>Missed</span>}

            {![ForgingStatusGenerated, ForgingStatusGenerating, ForgingStatusMissed].includes(status) && (
                <Tooltip content={tooltip}>
                    <span>{output}</span>
                </Tooltip>
            )}
        </div>
    );
}
