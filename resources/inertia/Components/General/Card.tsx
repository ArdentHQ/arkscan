import classNames from "@/utils/class-names";

export default function Card({
    children,
    className = "",
}: React.PropsWithChildren<{
    className?: string;
}>) {
    return (
        <div
            className={classNames({
                "rounded border border-theme-secondary-300 px-4 py-3 dark:border-theme-dark-700 md:rounded-xl md:px-6 md:py-4": true,
                [className]: true,
            })}
        >
            {children}
        </div>
    );
}
