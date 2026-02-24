import Tooltip from "@/Components/General/Tooltip";
import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function WalletOverviewItemEntry({
    title,
    value,
    tooltip,
    valueClassName,
    hasEmptyValue,
}: {
    title: string;
    value?: React.ReactNode | string | null;
    tooltip?: React.ReactNode | string;
    valueClassName?: string;
    hasEmptyValue?: boolean;
}) {
    const { t } = useTranslation();

    return (
        <div className="flex items-center justify-between space-x-2 text-sm font-semibold md:text-base">
            <div className="whitespace-nowrap dark:text-theme-dark-200">{title}</div>

            {(hasEmptyValue || !value) && (
                <div className="text-theme-secondary-500 dark:text-theme-dark-500">{t("general.na")}</div>
            )}

            {!hasEmptyValue && !!value && (
                <>
                    {tooltip && (
                        <Tooltip content={tooltip}>
                            <div
                                className={classNames([
                                    "text-theme-secondary-900 dark:text-theme-dark-50",
                                    valueClassName,
                                ])}
                            >
                                {value}
                            </div>
                        </Tooltip>
                    )}

                    {!tooltip && (
                        <div
                            className={classNames(["text-theme-secondary-900 dark:text-theme-dark-50", valueClassName])}
                        >
                            {value}
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
