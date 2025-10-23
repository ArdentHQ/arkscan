import Tooltip from "@/Components/General/Tooltip";
import { useTranslation } from "react-i18next";

export default function WalletOverviewItemEntry({
    title,
    value,
    tooltip,
    hasEmptyValue,
}: {
    title: string;
    value?: React.ReactNode | string | null;
    tooltip?: React.ReactNode | string;
    hasEmptyValue?: boolean;
}) {
    const { t } = useTranslation();

    return (
        <div className="flex justify-between items-center text-sm font-semibold md:text-base">
            <div className="dark:text-theme-dark-200">{title}</div>

            {(hasEmptyValue || !value) && (
                <div className="text-theme-secondary-500 dark:text-theme-dark-500">
                    {t("general.na")}
                </div>
            )}

            {!hasEmptyValue && !!value && (
                <>
                    {tooltip && (
                        <Tooltip content={tooltip}>
                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                {value}
                            </div>
                        </Tooltip>
                    )}

                    {!tooltip && (
                        <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                            {value}
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
