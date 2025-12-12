import classNames from "classnames";
import LoadingText from "../Loading/Text";

export default function Detail({
    title,
    titleClass = "",
    value = null,
    isLoading = false,
    className = "",
    children,
    ...props
}: React.PropsWithChildren<{
    title: string;
    titleClass?: string;
    value?: string | number | null;
    isLoading?: boolean;
    className?: string;
}>) {
    return (
        <div className="flex flex-col space-y-2 font-semibold" {...props}>
            <div
                className={classNames({
                    "whitespace-nowrap text-sm text-theme-secondary-700 dark:text-theme-dark-200": true,
                    [titleClass]: true,
                })}
            >
                {title}
            </div>

            <div
                className={classNames({
                    "leading-5 text-theme-secondary-900 dark:text-theme-dark-50": true,
                    [className]: true,
                })}
            >
                {isLoading && <LoadingText height="h-5" />}
                {!isLoading && !!value && <>{value}</>}
                {!isLoading && !value && <>{children}</>}
            </div>
        </div>
    );
}
