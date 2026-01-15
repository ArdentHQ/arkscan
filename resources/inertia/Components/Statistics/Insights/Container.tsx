import classNames from "classnames";
import Badge from "@/Components/General/Badge";

export default function InsightsContainer({
    title,
    fullWidth = false,
    applySpacing = false,
    children,
}: React.PropsWithChildren<{
    title?: string | null;
    fullWidth?: boolean;
    applySpacing?: boolean;
}>) {
    return (
        <div className="group/stats mt-3 px-6 first:mt-2 last:mb-8 dark:text-theme-dark-200 md:mx-auto md:max-w-7xl md:px-10">
            <div className="flex md:mt-0 md:space-x-3">
                <div className="ml-3 hidden w-[1.625rem] flex-col md:flex">
                    <div className="hidden h-[30px] w-full border-l-2 border-theme-secondary-300 group-first/stats:h-[18px] dark:border-theme-dark-700 md:-mt-3 md:block group-first/stats:md:-mt-0 group-first/stats:md:block" />

                    <div className="hidden min-h-[12px] w-full rounded-bl-xl border-b-2 border-l-2 border-theme-secondary-300 dark:border-theme-dark-700 md:block" />

                    <div className="hidden min-h-[12px] w-full flex-1 border-l-2 border-theme-secondary-300 group-last/stats:hidden dark:border-theme-dark-700 md:block" />
                </div>

                <div className="flex flex-1 flex-col space-y-3 rounded border border-theme-secondary-300 pb-4 font-semibold dark:border-theme-dark-700 md:space-y-0 md:rounded-xl md:pb-0">
                    {title && (
                        <div className="rounded-t bg-theme-secondary-100 px-4 py-3 text-sm dark:bg-theme-dark-950 md:hidden md:border-0 md:bg-transparent md:px-0 md:py-0 dark:md:bg-transparent">
                            {title}
                        </div>
                    )}

                    <div
                        className={classNames("flex flex-col px-4 text-sm md:px-6 md:py-4 md:text-base md:leading-5", {
                            "md-lg:w-2/3 md-lg:pr-10 xl:w-[524px] xl:pr-5": !fullWidth,
                            "space-y-3": applySpacing,
                        })}
                    >
                        {title && (
                            <div className="hidden md:inline-flex">
                                <Badge className="md:px-2 md:py-1 md:text-sm">{title}</Badge>
                            </div>
                        )}

                        <div className="flex flex-1 flex-col space-y-3 divide-y divide-dashed divide-theme-secondary-300 whitespace-nowrap dark:divide-theme-dark-700 md:divide-none">
                            {children}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
