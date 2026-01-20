import { Link } from "@inertiajs/react";
import Number from "@/Components/General/Number";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { StatisticsValidatorRow } from "@/Pages/Statistics.contracts";

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
                        {typeof row.value === "number" ? <Number>{row.value}</Number> : (row.value ?? t("general.na"))}
                    </div>
                </div>
            </div>
        </div>
    );
}
