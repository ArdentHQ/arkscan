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
                "rounded px-4 py-3 ring-1 ring-inset ring-theme-secondary-300 dark:ring-theme-dark-700 md:rounded-xl md:px-6 md:py-4": true,
                [className]: true,
            })}
        >
            {children}
        </div>
    );
}
