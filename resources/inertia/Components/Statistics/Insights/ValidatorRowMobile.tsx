import { Link } from "@inertiajs/react";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { StatisticsValidatorRow } from "@/Pages/Statistics.contracts";
import ValidatorRowValue from "./ValidatorRowValue";

export default function ValidatorRowMobile({ row }: { row: StatisticsValidatorRow }) {
    const { t } = useTranslation();

    const title = t(`pages.statistics.insights.validators.header.${row.key}`);
    const valueLabel = t(
        `pages.statistics.insights.validators.header.${row.key.includes("active_validator") ? "registered" : row.key === "most_blocks_forged" ? "blocks" : "voters"}`,
    );

    return (
        <div className="flex md:hidden">
            <div className="flex w-full flex-col justify-between space-y-3 pt-3 sm:flex-row sm:space-y-0">
                <div className="flex flex-col space-y-2">
                    <span>{title}</span>
                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {row.wallet ? (
                            <Link href={route("wallet", row.wallet.address)} className="link">
                                {row.wallet.hasUsername ? (
                                    row.wallet.username
                                ) : (
                                    <TruncateMiddle>{row.wallet.address}</TruncateMiddle>
                                )}
                            </Link>
                        ) : (
                            <span className="dark:text-theme-dark-200">{t("general.na")}</span>
                        )}
                    </span>
                </div>

                <div className="flex w-[90px] flex-col space-y-2">
                    <div>{valueLabel}</div>
                    <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                        <ValidatorRowValue rowKey={row.key} value={row.value} />
                    </div>
                </div>
            </div>
        </div>
    );
}
