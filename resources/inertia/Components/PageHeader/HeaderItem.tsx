import classNames from "classnames";
import { ReactNode } from "react";

interface HeaderItemProps {
    title?: string | null;
    background?: ReactNode;
    contentClass?: string;
    slotClass?: string;
    withoutPadding?: boolean;
    children?: ReactNode;
    className?: string;
}

export default function HeaderItem({
    title = null,
    background = null,
    contentClass = "",
    slotClass = "",
    withoutPadding = false,
    children,
    className = "",
}: HeaderItemProps) {
    return (
        <div
            className={classNames(
                "relative z-10 flex-1 overflow-hidden rounded font-semibold md:rounded-xl",
                className,
            )}
        >
            {background !== null && <div className="absolute z-20">{background}</div>}

            <div
                className={classNames(
                    "relative flex flex-col rounded md:rounded-xl",
                    {
                        "px-4 py-3 ring-1 ring-inset ring-theme-secondary-300 dark:text-theme-dark-50 dark:ring-theme-dark-700 md:px-6 md:py-4":
                            background === null,
                        "h-full p-4 md:px-6 md:py-4": !withoutPadding && background !== null,
                        "space-y-2": title !== null,
                    },
                    contentClass,
                )}
            >
                {title && <div className="text-sm dark:text-theme-dark-200">{title}</div>}

                <div className={classNames("leading-5 text-theme-secondary-900 dark:text-theme-dark-50", slotClass)}>
                    {children}
                </div>
            </div>
        </div>
    );
}
