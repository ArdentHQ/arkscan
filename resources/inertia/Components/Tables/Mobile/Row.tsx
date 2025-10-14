import DropdownArrow from "@/Components/General/Dropdown/Arrow";
import classNames from "@/utils/class-names";
import { useState } from "react";

export default function MobileTableRow({
    header,
    expandable = false,
    expandClass = '',
    expandDisabled = false,
    contentClass = '',
    className = '',
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
        <div className={classNames({
            'text-sm rounded border border-theme-secondary-300 dark:border-theme-dark-700': true,
            [className]: true,
        })}>
            <div
                className={classNames({
                    "flex justify-between items-center rounded-t bg-theme-secondary-100 dark:bg-theme-dark-950 py-3 px-4 font-semibold": true,
                    "rounded-b": !children && (!expandable || (!isExpanded && expandable)),
                    "sm:rounded-b-none": expandable,
                    [expandClass]: expandable,
                })}
            >
                {header}

                {expandable && (
                    <div className="flex items-center pl-3 sm:hidden h-[17px]">
                        <DropdownArrow
                            isOpen={isExpanded}
                            color={classNames({
                                'text-theme-secondary-300 dark:text-theme-dark-800': expandDisabled,
                                'text-theme-secondary-700 dark:text-theme-dark-200': !expandDisabled,
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
                        "flex flex-col px-4 pt-3 pb-4 space-y-4 sm:flex-row sm:flex-1 sm:justify-between sm:space-y-0": true,
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
