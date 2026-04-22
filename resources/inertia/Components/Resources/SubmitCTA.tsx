import classNames from "classnames";

export default function SubmitCTA({
    title,
    subtitle,
    button,
    onClick,
    className = "mt-6 sm:flex-row sm:space-y-0 sm:py-2 sm:text-start",
    titleClassName = "sm:text-lg",
}: {
    title: string;
    subtitle: string;
    button: string;
    onClick: () => void;
    className?: string;
    titleClassName?: string;
}) {
    return (
        <div
            className={classNames([
                "bg-theme-primary-100 dark:bg-theme-dark-800 flex w-full flex-col items-center justify-between space-y-3 rounded-xl px-6 py-6 text-center",
                className,
            ])}
        >
            <span
                className={classNames(
                    "text-theme-primary-900 dim:text-theme-dark-50 space-x-1 font-semibold dark:text-white",
                    titleClassName,
                )}
            >
                <span>{title}</span>

                <span className="whitespace-nowrap">{subtitle}</span>
            </span>

            <button type="button" className="button-primary w-full sm:w-auto" onClick={onClick}>
                {button}
            </button>
        </div>
    );
}
