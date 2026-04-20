import { useEffect, useState } from "react";
import classNames from "classnames";
import { useValidatorStatus } from "@/Providers/ValidatorStatus/ValidatorStatusContext";
import {
    ForgingStatusGenerated,
    ForgingStatusGenerating,
    ForgingStatusMissed,
} from "@/Providers/ValidatorStatus/types";
import Tooltip from "@/Components/General/Tooltip";
import { formatDayjsDateTime } from "@/utils/formatter";

export default function TimeToForge({
    className = "text-theme-secondary-900 dark:text-theme-dark-50",
}: {
    className?: string;
}) {
    const [tooltip, setTooltip] = useState<string>();

    const { dateTime, output, status } = useValidatorStatus();

    useEffect(() => {
        setTooltip(formatDayjsDateTime(dateTime));
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
