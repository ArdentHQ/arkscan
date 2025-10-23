import classNames from "@/utils/class-names";

export default function Badge({
    colors = "border-transparent bg-theme-secondary-200 dark:border-theme-dark-700 dark:text-theme-dark-200",
    className = "",
    children,
}: {
    colors?: string;
    className?: string;
    children: React.ReactNode;
}) {
    return (
        <div
            className={classNames({
                "shrink-0 rounded border px-[3px] py-[2px] text-xs font-semibold leading-3.75 dark:bg-transparent": true,
                [className]: true,
                [colors]: true,
            })}
        >
            {children}
        </div>
    );
}
