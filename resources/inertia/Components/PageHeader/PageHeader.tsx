export default function PageHeader({
    title,
    subtitle,
    children,
    right,
}: React.PropsWithChildren<{
    title: string;
    subtitle?: string;
    right?: React.ReactNode;
}>) {
    return (
        <div className="flex flex-col space-y-6 px-6 pb-6 font-semibold md:mx-auto md:max-w-7xl md:px-10">
            <div className="md-lg:flex-row md-lg:items-center md-lg:space-y-0 flex w-full flex-col space-y-6 sm:space-y-4 md:justify-between">
                <div className="flex flex-col space-y-1.5">
                    <h1 className="text-lg leading-5.25 md:text-2xl md:leading-[1.8125rem]">{title}</h1>

                    <span className="text-theme-secondary-500 dark:text-theme-dark-200 text-xs leading-3.75">
                        {subtitle}
                    </span>
                </div>

                {right}
            </div>

            {children && (
                <div className="flex flex-col space-y-2 sm:space-y-3 xl:flex-row xl:space-y-0 xl:space-x-3">
                    {children}
                </div>
            )}
        </div>
    );
}
