import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function MarketStat({
    label,
    value,
    suffix,
    disabled = false,
}: {
    label: string;
    value?: React.ReactNode;
    suffix?: string;
    disabled?: boolean;
}) {
    const { t } = useTranslation();
    const isDisabled = disabled || value === null || value === undefined || value === "";

    return (
        <div className="flex flex-col space-y-2">
            <div className="text-theme-secondary-700 dark:text-theme-dark-200 text-sm font-semibold">{label}</div>

            <div
                className={classNames("flex items-baseline text-base leading-5! font-semibold", {
                    "text-theme-secondary-900 dark:text-theme-dark-50": !isDisabled,
                    "text-theme-secondary-500 dark:text-theme-dark-500": isDisabled,
                })}
            >
                {isDisabled ? (
                    <span>{t("general.na")}</span>
                ) : (
                    <>
                        <span>{value}</span>
                        {suffix && <span className="ml-1">{suffix}</span>}
                    </>
                )}
            </div>
        </div>
    );
}
