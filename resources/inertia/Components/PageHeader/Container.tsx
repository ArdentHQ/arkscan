import classNames from "classnames";

export default function PageHeaderContainer({
    label,
    children,
    breakpoint = "md",
    extra,
}: {
    label: string;
    children: React.ReactNode;
    breakpoint?: "sm" | "md";
    extra?: React.ReactNode;
}) {
    return (
        <div className="flex flex-col px-6 pb-6 pt-8 md:mx-auto md:max-w-7xl md:px-10">
            <div
                className={classNames({
                    "flex flex-col space-y-4 overflow-hidden font-semibold sm:flex-row sm:items-end sm:justify-between sm:space-y-0": true,
                    "sm:items-center sm:rounded-lg sm:border sm:border-theme-secondary-300 sm:dark:border-theme-dark-700":
                        breakpoint === "sm",
                    "md:items-center md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-dark-700":
                        breakpoint === "md",
                })}
            >
                <div
                    className={classNames({
                        "flex min-w-0 flex-col space-y-2": true,
                        "sm:flex-row sm:items-center sm:space-x-3 sm:space-y-0": breakpoint === "sm",
                        "md:flex-row md:items-center md:space-x-3 md:space-y-0": breakpoint === "md",
                    })}
                >
                    <div
                        className={classNames({
                            "whitespace-nowrap text-sm !leading-4.25 dark:text-theme-dark-200": true,
                            "sm:bg-theme-secondary-200 sm:px-4 sm:py-[14.5px] sm:text-base sm:text-lg sm:!leading-5.25 sm:dark:bg-black":
                                breakpoint === "sm",
                            "md:bg-theme-secondary-200 md:px-4 md:py-[14.5px] md:text-lg md:!leading-5.25 md:dark:bg-black":
                                breakpoint === "md",
                        })}
                    >
                        {label}
                    </div>

                    <div
                        className={classNames({
                            "min-w-0 leading-5 text-theme-secondary-900 dark:text-theme-dark-50": true,
                            "sm:text-lg sm:leading-5.25": breakpoint === "sm",
                            "md:text-lg md:leading-5.25": breakpoint === "md",
                        })}
                    >
                        {children}
                    </div>
                </div>

                {extra && (
                    <div
                        className={classNames({
                            "flex w-full space-x-2 sm:w-auto": true,
                            "sm:px-4": breakpoint === "sm",
                            "md:px-4": breakpoint === "md",
                        })}
                    >
                        {extra}
                    </div>
                )}
            </div>
        </div>
    );
}
