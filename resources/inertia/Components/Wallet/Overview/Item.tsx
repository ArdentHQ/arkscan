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
                "border-theme-secondary-200 flex flex-1 flex-col space-y-3 border-t-4 p-6 md:border-0 md:p-0 dark:border-black": true,
                [className]: !!className,
            })}
        >
            <div className="dark:text-theme-dark-200 flex justify-between font-semibold">
                {title}

                {titleExtra}
            </div>

            <div className="border-theme-secondary-300 dark:border-theme-dark-800 relative flex-1 md:rounded-xl md:border">
                <div className="relative flex flex-col space-y-3 md:p-6">{children}</div>

                {maskedMessage && (
                    <div className="text-theme-secondary-500 absolute inset-0 -mx-6 -my-2 flex items-center justify-center text-sm font-semibold backdrop-blur select-none md:m-0 md:rounded-xl">
                        {maskedMessage}
                    </div>
                )}
            </div>
        </div>
    );
}
