import classNames from "classnames";

export default function MobileTable({
    noResultsMessage,
    className = "",
    resultCount,
    children,
}: React.PropsWithChildren<{
    noResultsMessage?: string;
    className?: string;
    resultCount?: number;
}>) {
    return (
        <>
            {resultCount === 0 ? (
                <div className="leading-7 dark:text-theme-dark-200">{noResultsMessage}</div>
            ) : (
                <>
                    <div
                        className={classNames({
                            "table-container mb-4": true,
                            [className]: !!className,
                        })}
                    >
                        <div
                            className={classNames({
                                "table-list-mobile table-list-encapsulated flex flex-col space-y-3": true,
                                [className]: true,
                            })}
                        >
                            {children}
                        </div>
                    </div>
                </>
            )}
        </>
    );
}
