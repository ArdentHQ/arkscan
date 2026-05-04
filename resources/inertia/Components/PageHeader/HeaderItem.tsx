import classNames from "classnames";
import { ReactNode } from "react";

interface HeaderItemProps {
    title?: string | null;
    background?: ReactNode;
    contentClass?: string;
    slotClass?: string;
    withoutPadding?: boolean;
    backgroundClass?: string;
    children?: ReactNode;
    className?: string;
}

export default function HeaderItem({
    title = null,
    background = null,
    contentClass = "",
    slotClass = "",
    backgroundClass = "",
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
            {background !== null && <div className={classNames("absolute z-20", backgroundClass)}>{background}</div>}

            <div
                className={classNames(
                    "relative flex flex-col rounded md:rounded-xl",
                    {
                        "ring-theme-secondary-300 dark:text-theme-dark-50 dark:ring-theme-dark-700 px-4 py-3 ring-1 ring-inset md:px-6 md:py-4":
                            background === null,
                        "h-full p-4 md:px-6 md:py-4": !withoutPadding && background !== null,
                        "space-y-2": title !== null,
                    },
                    contentClass,
                )}
            >
                {title && <div className="dark:text-theme-dark-200 text-sm">{title}</div>}

                <div className={classNames("text-theme-secondary-900 dark:text-theme-dark-50 leading-5", slotClass)}>
                    {children}
                </div>
            </div>
        </div>
    );
}
