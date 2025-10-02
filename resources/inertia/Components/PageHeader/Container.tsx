import classNames from "@/utils/class-names"

export default function PageHeaderContainer({
    label,
    children,
    breakpoint = 'md',
    extra,
}: {
    label: string;
    children: React.ReactNode;
    breakpoint?: 'sm' | 'md';
    extra?: React.ReactNode;
}) {
    return (
        <div className="flex flex-col px-6 pt-8 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
            <div className={classNames({
                'flex overflow-hidden flex-col space-y-4 font-semibold sm:flex-row sm:justify-between sm:items-end sm:space-y-0': true,
                'sm:items-center sm:rounded-lg sm:border sm:border-theme-secondary-300 sm:dark:border-theme-dark-700': breakpoint === 'sm',
                'md:items-center md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-dark-700': breakpoint === 'md',
            })}>
                <div className={classNames({
                    'flex flex-col min-w-0 space-y-2': true,
                    'sm:flex-row sm:items-center sm:space-y-0 sm:space-x-3': breakpoint === 'sm',
                    'md:flex-row md:items-center md:space-y-0 md:space-x-3': breakpoint === 'md',
                })}>
                    <div className={classNames({
                        'text-sm dark:text-theme-dark-200 !leading-4.25 whitespace-nowrap': true,
                        'sm:px-4 sm:py-[14.5px] sm:bg-theme-secondary-200 sm:dark:bg-black sm:text-base sm:text-lg sm:!leading-5.25': breakpoint === 'sm',
                        'md:px-4 md:py-[14.5px] md:bg-theme-secondary-200 md:dark:bg-black md:text-lg md:!leading-5.25': breakpoint === 'md',
                    })}>
                        {label}
                    </div>

                    <div className={classNames({
                        'min-w-0 leading-5 text-theme-secondary-900 dark:text-theme-dark-50': true,
                        'sm:leading-5.25 sm:text-lg': breakpoint === 'sm',
                        'md:leading-5.25 md:text-lg': breakpoint === 'md',
                    })}>
                        {children}
                    </div>
                </div>

                {extra && <div className={classNames({
                    'flex space-x-2 w-full sm:w-auto': true,
                    'sm:px-4': breakpoint === 'sm',
                    'md:px-4': breakpoint === 'md',
                })}>
                    {extra}
                </div>}
            </div>
        </div>
    );
}
