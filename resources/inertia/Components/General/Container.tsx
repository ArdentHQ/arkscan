import classNames from "classnames";

export default function Container({
    children,
    className = "",
}: React.PropsWithChildren<{
    className?: string;
}>) {
    return (
        <div
            className={classNames({
                "border-theme-secondary-300 dark:border-theme-dark-700 rounded border px-4 py-3 md:rounded-xl md:px-6 md:py-4": true,
                [className]: true,
            })}
        >
            {children}
        </div>
    );
}
