import classNames from "classnames";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";

export default function DropdownArrow({
    isOpen = false,
    color = "text-theme-secondary-700 dark:text-theme-dark-200",
    onClick = () => {},
}: {
    isOpen?: boolean;
    color?: string;
    onClick?: () => void;
}) {
    return (
        <div
            className={classNames({
                "transition-default": true,
                "rotate-180": isOpen,
                [color]: true,
            })}
            onClick={onClick}
        >
            <ChevronDownSmallIcon className="h-3 w-3" />
        </div>
    );
}
