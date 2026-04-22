import classNames from "classnames";

interface PageSectionProps {
    title?: string;
    children: React.ReactNode;
    noBorder?: boolean;
    wrapperContainerClass?: string;
    borderClass?: string;
    className?: string;
    wrapperClass?: string;
}

export default function PageSection({
    title,
    children,
    noBorder = false,
    wrapperContainerClass = "",
    borderClass = "sm:border-theme-secondary-300 dark:border-theme-dark-700",
    className = "",
    wrapperClass = "flex flex-1 flex-col space-y-3 whitespace-nowrap sm:overflow-x-auto",
}: PageSectionProps) {
    return (
        <div
            className={classNames(
                "group dark:text-theme-dark-200 px-3 sm:px-6 md:mx-auto md:max-w-7xl md:px-10",
                className,
            )}
        >
            <div className="mt-6 flex group-first:mt-0 sm:mt-0 sm:space-x-3 group-first:sm:-mt-2">
                <div className="ml-3 hidden w-[1.625rem] flex-col sm:flex">
                    <div className="border-theme-secondary-300 dark:border-theme-dark-700 -mt-2 hidden h-[9px] w-full border-l-2 sm:block group-first:sm:block" />
                    <div className="border-theme-secondary-300 dark:border-theme-dark-700 hidden min-h-[12px] w-full rounded-bl-xl border-b-2 border-l-2 sm:block" />
                    <div className="border-theme-secondary-300 dark:border-theme-dark-700 hidden min-h-[12px] w-full flex-1 border-l-2 group-last:hidden sm:block" />
                </div>

                <div className="flex min-w-0 flex-1 flex-col space-y-3 font-semibold sm:space-y-2 sm:pb-4">
                    {title && (
                        <div className="border-theme-primary-400 bg-theme-secondary-100 dark:border-theme-dark-blue-400 dark:bg-theme-dark-950 border-l-2 px-3 py-2 sm:border-0 sm:bg-transparent sm:px-0 sm:py-0 dark:sm:bg-transparent">
                            {title}
                        </div>
                    )}

                    <div
                        className={classNames({
                            "flex space-x-4 text-sm sm:rounded-xl sm:text-base sm:leading-5": true,
                            "px-3 sm:border sm:px-6 sm:py-4": !noBorder,
                            [borderClass]: !noBorder,
                            [wrapperContainerClass]: !!wrapperContainerClass,
                        })}
                    >
                        <div className={wrapperClass}>{children}</div>
                    </div>
                </div>
            </div>
        </div>
    );
}
