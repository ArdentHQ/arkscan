export default function PageHeader({ title, subtitle, children }: React.PropsWithChildren<{
    title: string;
    subtitle?: string;
}>) {
    return (
        <div className="flex flex-col px-6 pt-8 pb-6 space-y-6 font-semibold md:px-10 md:mx-auto md:max-w-7xl">
            <div className="flex flex-col space-y-1.5">
                <h1 className="mb-0 text-lg font-semibold md:text-2xl leading-5.25 md:leading-[1.8125rem]">
                    {title}
                </h1>

                <span className="text-xs leading-3.75 text-theme-secondary-500 dark:text-theme-dark-200">
                    {subtitle}
                </span>
            </div>

            {children && (
                <div className="flex flex-col space-y-2 sm:space-y-3 xl:flex-row xl:space-y-0 xl:space-x-3">
                    {children}
                </div>
            )}
        </div>
    );
}
