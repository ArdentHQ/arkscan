import classNames from "@/utils/class-names";

export default function MobileTable({
    noResultsMessage,
    className = 'px-6 md:px-10',
    children,
}: React.PropsWithChildren<{
    noResultsMessage?: string;
    className?: string;
}>) {
    return (
        <div className={classNames({
            "table-container": true,
            [className]: !! className,
        })}>
            <div className={classNames({
                'flex flex-col space-y-3 table-list-mobile table-list-encapsulated': true,
                [className]: true,
            })}>
                {!!noResultsMessage ? (
                    <div className="dark:text-theme-dark-200">
                        {noResultsMessage}
                    </div>
                ) : (<>
                    {children}
                </>)}
            </div>
        </div>
    )
}
