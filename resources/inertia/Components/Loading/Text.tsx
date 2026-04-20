import classNames from "classnames";

export default function LoadingText({
    width = "w-[70px]",
    height = "h-[17px]",
    wrapperClass = "",
}: React.PropsWithChildren<{
    width?: string;
    height?: string;
    wrapperClass?: string;
}>) {
    return (
        <div className={wrapperClass}>
            <div
                className={classNames({
                    "animate-pulse rounded-sm-md bg-theme-secondary-300 dark:bg-theme-dark-800": true,
                    [width]: true,
                    [height]: true,
                })}
            ></div>
        </div>
    );
}
