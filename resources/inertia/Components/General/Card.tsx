import classNames from "classnames";

export default function Card({
    children,
    className = "",
}: React.PropsWithChildren<{
    className?: string;
}>) {
    return (
        <div
            className={classNames({
                "ring-theme-secondary-300 dark:ring-theme-dark-700 rounded px-4 py-3 ring-1 ring-inset md:rounded-xl md:px-6 md:py-4": true,
                [className]: true,
            })}
        >
            {children}
        </div>
    );
}
