import { DATE_TIME_FORMAT } from "@/constants";
import classNames from "@/utils/class-names";
import dayjs from "dayjs";
import dayjsRelativeTime from "dayjs/plugin/relativeTime";
import Tooltip from "../General/Tooltip";

dayjs.extend(dayjsRelativeTime);

export default function Age({
    timestamp,
    className = "text-theme-secondary-900 dark:text-theme-dark-50",
}: {
    timestamp: number;
    className?: string;
}) {
    const date = dayjs(timestamp * 1000);
    const formattedAge = dayjs().to(date);

    return (
        <Tooltip content={date.format(DATE_TIME_FORMAT)}>
            <span
                className={classNames({
                    "text-sm font-semibold leading-4.25": true,
                    [className]: true,
                })}
            >
                {formattedAge}
            </span>
        </Tooltip>
    );
}
