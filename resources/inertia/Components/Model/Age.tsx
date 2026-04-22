import classNames from "classnames";
import { formatAge } from "@/utils/formatter";
import Tooltip from "../General/Tooltip";

export default function Age({
    timestamp,
    className = "text-theme-secondary-900 dark:text-theme-dark-50",
}: {
    timestamp: number;
    className?: string;
}) {
    const { relative, tooltip } = formatAge(timestamp);

    return (
        <Tooltip content={tooltip}>
            <span
                className={classNames({
                    "text-sm leading-4.25 font-semibold": true,
                    [className]: true,
                })}
            >
                {relative}
            </span>
        </Tooltip>
    );
}
