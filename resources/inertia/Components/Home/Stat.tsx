import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function HomeStat({
    title,
    disabled = false,
    className = "",
    children,
}: {
    title: string;
    disabled?: boolean;
    className?: string;
    children?: React.ReactNode;
}) {
    const { t } = useTranslation();

    return (
        <div className={classNames("space-y-2", className)}>
            <div className="text-sm font-semibold dark:text-theme-dark-200">{title}</div>

            <div
                className={classNames("font-semibold !leading-5 text-sm md:text-base", {
                    "text-theme-secondary-900 dark:text-theme-dark-50": !disabled,
                    "text-theme-secondary-500 dark:text-theme-dark-500": disabled,
                })}
            >
                {disabled ? t("general.na") : children}
            </div>
        </div>
    );
}
