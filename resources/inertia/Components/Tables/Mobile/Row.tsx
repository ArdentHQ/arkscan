import DropdownArrow from "@/Components/General/Dropdown/Arrow";
import classNames from "classnames";
import { useState } from "react";

export default function MobileTableRow({
    header,
    expandable = false,
    expandClass = "space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700",
    expandDisabled = false,
    contentClass = "",
    className = "",
    children,
}: React.PropsWithChildren<{
    header: React.ReactNode;
    expandable?: boolean;
    expandClass?: string;
    expandDisabled?: boolean;
    contentClass?: string;
    className?: string;
}>) {
    const [isExpanded, setIsExpanded] = useState(expandable ? false : true);

    return (
        <div
            className={classNames({
                "border-theme-secondary-300 dark:border-theme-dark-700 rounded border text-sm": true,
                [className]: true,
            })}
        >
            <div
                className={classNames({
                    "bg-theme-secondary-100 dark:bg-theme-dark-950 flex items-center justify-between rounded-t px-4 py-3 font-semibold":
                        true,
                    "rounded-b": (!children && !expandable) || (!!children && !isExpanded && expandable),
                    "sm:rounded-b-none": expandable,
                    [expandClass]: expandable,
                })}
            >
                {header}

                {expandable && (
                    <div className="flex h-[17px] items-center sm:hidden">
                        <DropdownArrow
                            isOpen={isExpanded}
                            color={classNames({
                                "text-theme-secondary-300 dark:text-theme-dark-800": expandDisabled,
                                "text-theme-secondary-700 dark:text-theme-dark-200": !expandDisabled,
                            })}
                            onClick={() => {
                                if (expandDisabled) {
                                    return;
                                }

                                setIsExpanded(!isExpanded);
                            }}
                        />
                    </div>
                )}
            </div>

            {children && (
                <div
                    className={classNames({
                        "flex flex-col space-y-4 px-4 pt-3 pb-4 sm:flex-1 sm:flex-row sm:justify-between sm:space-y-0":
                            true,
                        "hidden sm:flex": !isExpanded,
                        [contentClass]: true,
                    })}
                >
                    {children}
                </div>
            )}
        </div>
    );
}
