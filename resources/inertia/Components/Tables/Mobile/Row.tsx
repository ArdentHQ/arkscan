import DropdownArrow from "@/Components/General/Dropdown/Arrow";
import classNames from "@/utils/class-names";
import { useState } from "react";

export default function MobileTableRow({
    header,
    expandable = false,
    expandClass = "",
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
                "rounded border border-theme-secondary-300 text-sm dark:border-theme-dark-700": true,
                [className]: true,
            })}
        >
            <div
                className={classNames({
                    "flex items-center justify-between rounded-t bg-theme-secondary-100 px-4 py-3 font-semibold dark:bg-theme-dark-950": true,
                    "rounded-b": !children && (!expandable || (!isExpanded && expandable)),
                    "sm:rounded-b-none": expandable,
                    [expandClass]: expandable,
                })}
            >
                {header}

                {expandable && (
                    <div className="flex h-[17px] items-center pl-3 sm:hidden">
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
                        "flex flex-col space-y-4 px-4 pb-4 pt-3 sm:flex-1 sm:flex-row sm:justify-between sm:space-y-0": true,
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
