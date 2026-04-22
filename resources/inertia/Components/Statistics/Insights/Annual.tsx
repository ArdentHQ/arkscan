import { useTranslation } from "react-i18next";
import InsightsContainer from "./Container";
import Number from "@/Components/General/Number";
import { StatisticsAnnualRow } from "@/Pages/Statistics.contracts";
import { networkCurrency } from "@/utils/number-formatter";

export default function AnnualInsights({ years, activeTab }: { years: StatisticsAnnualRow[]; activeTab: string }) {
    const { t } = useTranslation();

    return (
        <div className={activeTab !== "annual" ? "hidden md:block" : undefined}>
            <div className="text-theme-secondary-900 dark:text-theme-dark-50 hidden px-6 font-semibold md:mx-auto md:block md:max-w-7xl md:px-10">
                {t("pages.statistics.insights.annual.title")}
            </div>

            <div>
                <div className="-mt-2 flex flex-col md:hidden">
                    {years.map((year) => (
                        <InsightsContainer key={year.year} title={String(year.year)} fullWidth>
                            <div className="divide-theme-secondary-300 dark:divide-theme-dark-700 flex w-full flex-1 flex-col justify-between space-y-3 divide-y divide-dashed sm:flex-row sm:space-y-0 sm:divide-none">
                                <div className="flex flex-col space-y-2">
                                    <span>{t("pages.statistics.insights.annual.header.transaction")}</span>
                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        <Number>{year.transactions}</Number>
                                    </span>
                                </div>

                                <div className="flex w-full flex-col space-y-2 pt-3 sm:w-[170px] sm:pt-0">
                                    <span>{t("pages.statistics.insights.annual.header.volume")}</span>
                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        {networkCurrency(year.volume, 2, true)}
                                    </span>
                                </div>
                            </div>

                            <div className="divide-theme-secondary-300 dark:divide-theme-dark-700 flex w-full flex-1 flex-col justify-between space-y-3 divide-y divide-dashed sm:flex-row sm:space-y-0 sm:divide-none">
                                <div className="flex flex-col space-y-2">
                                    <span>{t("pages.statistics.insights.annual.header.fees")}</span>
                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        {networkCurrency(year.fees, year.fees < 1 ? 4 : 2, true)}
                                    </span>
                                </div>

                                <div className="flex w-full flex-col space-y-2 pt-3 sm:w-[170px] sm:pt-0">
                                    <span>{t("pages.statistics.insights.annual.header.blocks")}</span>
                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        <Number>{year.blocks}</Number>
                                    </span>
                                </div>
                            </div>
                        </InsightsContainer>
                    ))}
                </div>

                <div className="hidden md:block xl:hidden">
                    <InsightsContainer fullWidth>
                        <div className="divide-theme-secondary-300 dark:divide-theme-dark-700 flex flex-col space-y-4 divide-y divide-dashed">
                            {years.map((year, index) => (
                                <div key={year.year} className={index > 0 ? "flex pt-4" : "flex"}>
                                    <div className="text-theme-secondary-900 dark:text-theme-dark-50 flex flex-1">
                                        {year.year}
                                    </div>
                                    <div className="md-lg:flex-2 md-lg:flex-row md-lg:space-y-0 flex flex-1 flex-col space-y-3">
                                        <div className="flex flex-1 flex-col space-y-3">
                                            <div className="flex justify-between space-x-3">
                                                <span>{t("pages.statistics.insights.annual.header.transaction")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    <Number>{year.transactions}</Number>
                                                </span>
                                            </div>
                                            <div className="flex justify-between space-x-3">
                                                <span>{t("pages.statistics.insights.annual.header.blocks")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    <Number>{year.blocks}</Number>
                                                </span>
                                            </div>
                                        </div>
                                        <div className="md-lg:pl-16 flex flex-1 flex-col space-y-3">
                                            <div className="flex justify-between space-x-3">
                                                <span>{t("pages.statistics.insights.annual.header.volume")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    {networkCurrency(year.volume, 2, true)}
                                                </span>
                                            </div>
                                            <div className="flex justify-between space-x-3">
                                                <span>{t("pages.statistics.insights.annual.header.fees")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    {networkCurrency(year.fees, year.fees < 1 ? 4 : 2, true)}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </InsightsContainer>
                </div>

                <div className="hidden xl:block">
                    <InsightsContainer fullWidth>
                        <table className="w-full border-separate border-spacing-y-3">
                            <tbody>
                                {years.map((year) => (
                                    <tr key={year.year}>
                                        <td>
                                            <div className="text-theme-secondary-900 dark:text-theme-dark-50 pr-8">
                                                {year.year}
                                            </div>
                                        </td>
                                        <td>
                                            <div className="flex justify-between space-x-3 px-8">
                                                <span>{t("pages.statistics.insights.annual.header.transaction")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    <Number>{year.transactions}</Number>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div className="flex justify-between space-x-3 px-8">
                                                <span>{t("pages.statistics.insights.annual.header.volume")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    {networkCurrency(year.volume, 2, true)}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div className="flex justify-between space-x-3 px-8">
                                                <span>{t("pages.statistics.insights.annual.header.fees")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    {networkCurrency(year.fees, year.fees < 1 ? 4 : 2, true)}
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div className="flex justify-between space-x-3 pl-8">
                                                <span>{t("pages.statistics.insights.annual.header.blocks")}:</span>
                                                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    <Number>{year.blocks}</Number>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </InsightsContainer>
                </div>
            </div>
        </div>
    );
}
