import classNames from "classnames";

export default function WalletOverviewItem({
    title,
    titleExtra,
    children,
    maskedMessage = null,
    className = "",
}: {
    title: string;
    titleExtra?: React.ReactNode;
    children: React.ReactNode;
    maskedMessage?: React.ReactNode | null;
    className?: string;
}) {
    return (
        <div
            className={classNames({
                "flex flex-1 flex-col space-y-3 border-t-4 border-theme-secondary-200 p-6 dark:border-black md:border-0 md:p-0": true,
                [className]: !!className,
            })}
        >
            <div className="flex justify-between font-semibold dark:text-theme-dark-200">
                {title}

                {titleExtra}
            </div>

            <div className="relative flex-1 border-theme-secondary-300 dark:border-theme-dark-800 md:rounded-xl md:border">
                <div className="relative flex flex-col space-y-3 md:p-6">{children}</div>

                {maskedMessage && (
                    <div className="absolute inset-0 -mx-6 -my-2 flex select-none items-center justify-center text-sm font-semibold text-theme-secondary-500 backdrop-blur md:m-0 md:rounded-xl">
                        {maskedMessage}
                    </div>
                )}
            </div>
        </div>
    );
}
