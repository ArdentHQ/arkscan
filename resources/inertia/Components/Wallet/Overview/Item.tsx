import classNames from "@/utils/class-names";

export default function WalletOverviewItem({
    title,
    titleExtra,
    children,
    maskedMessage = null,
    className = '',
}: {
    title: string;
    titleExtra?: React.ReactNode;
    children: React.ReactNode;
    maskedMessage?: React.ReactNode | null;
    className?: string;
}) {
    return (
        <div className={classNames({
            "flex flex-col flex-1 p-6 space-y-3 border-t-4 md:p-0 md:border-0 dark:border-black border-theme-secondary-200": true,
            [className]: !! className,
        })}>
            <div className="flex justify-between font-semibold dark:text-theme-dark-200">
                {title}

                {titleExtra}
            </div>

            <div className="relative flex-1 md:rounded-xl md:border border-theme-secondary-300 dark:border-theme-dark-800">
                <div className="flex relative flex-col space-y-3 md:p-6">
                    {children}
                </div>

                {maskedMessage && (
                    <div className="flex absolute inset-0 justify-center items-center -my-2 -mx-6 text-sm font-semibold select-none md:m-0 md:rounded-xl backdrop-blur text-theme-secondary-500">
                        {maskedMessage}
                    </div>
                )}
            </div>
        </div>
    )
}
