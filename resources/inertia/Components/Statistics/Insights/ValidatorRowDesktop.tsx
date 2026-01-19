import { Link } from "@inertiajs/react";
import Number from "@/Components/General/Number";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { StatisticsValidatorRow } from "@/Pages/Statistics.contracts";

export default function ValidatorRowDesktop({ row }: { row: StatisticsValidatorRow }) {
    const { t } = useTranslation();

    const title = t(`pages.statistics.insights.validators.header.${row.key}`);
    const valueLabel = t(
        `pages.statistics.insights.validators.header.${row.key.includes("active_validator") ? "registered" : row.key === "most_blocks_forged" ? "blocks" : "voters"}`,
    );

    return (
        <div className="hidden w-full justify-between md:flex xl:w-[770px]">
            <div className="flex flex-1">{title}</div>
            <div className="flex flex-1 flex-col justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                <div className="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
                    {row.wallet ? (
                        <Link href={route("wallet", row.wallet.address)} className="link">
                            {row.wallet.hasUsername ? (
                                row.wallet.username
                            ) : (
                                <TruncateMiddle>{row.wallet.address}</TruncateMiddle>
                            )}
                        </Link>
                    ) : (
                        <div className="dark:text-theme-dark-200">{t("general.na")}</div>
                    )}
                </div>

                <div className="flex w-full flex-1 justify-between space-x-2 md-lg:pl-16">
                    <div>{valueLabel}:</div>
                    <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {typeof row.value === "number" ? <Number>{row.value}</Number> : (row.value ?? t("general.na"))}
                    </div>
                </div>
            </div>
        </div>
    );
}
