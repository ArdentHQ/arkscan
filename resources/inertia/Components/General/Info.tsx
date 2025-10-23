import HintSmallIcon from "@ui/icons/hint-small.svg?react";
import QuestionMarkSmallIcon from "@ui/icons/question-mark-small.svg?react";
import classNames from "@/utils/class-names";
import { createElement } from "react";
import Tooltip from "./Tooltip";

function InfoComponent({
    tooltip,
    large = false,
    className = "",
    icon,
}: {
    tooltip?: string;
    large?: boolean;
    className?: string;
    icon: React.ElementType;
}) {
    return (
        <div
            aria-label={tooltip}
            className={classNames({
                "transition-default inline-block cursor-pointer rounded-full bg-theme-primary-100 text-theme-primary-600 outline-none hover:bg-theme-primary-700 hover:text-white focus-visible:ring-2 focus-visible:ring-theme-primary-500 dark:bg-theme-secondary-800 dark:text-theme-secondary-600 dark:hover:bg-theme-secondary-600 dark:hover:text-theme-secondary-800": true,
                "p-1.5": large,
                "p-1": !large,
                [className]: !!className,
            })}
        >
            {createElement(icon, {
                className: classNames({
                    "w-3 h-3": !large,
                    "w-4 h-4": large,
                }),
            })}
        </div>
    );
}

export default function Info({
    tooltip,
    type = "question",
    large = false,
    className = "",
}: {
    tooltip?: string;
    type?: "question" | "info";
    large?: boolean;
    className?: string;
}) {
    let iconOutput = HintSmallIcon;
    if (type === "question") {
        iconOutput = QuestionMarkSmallIcon;
    }

    return (
        <>
            {!!tooltip && (
                <Tooltip content={tooltip}>
                    <InfoComponent tooltip={tooltip} large={large} className={className} icon={iconOutput} />
                </Tooltip>
            )}

            {!tooltip && <InfoComponent large={large} className={className} icon={iconOutput} />}
        </>
    );
}
