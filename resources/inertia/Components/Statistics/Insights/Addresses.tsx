import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import InsightsContainer from "./Container";
import Number from "@/Components/General/Number";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import useSharedData from "@/hooks/use-shared-data";
import { StatisticsAddressInsights } from "@/Pages/Statistics.contracts";
import { networkCurrency } from "@/utils/number-formatter";

export default function AddressInsights({ data, activeTab }: { data: StatisticsAddressInsights; activeTab: string }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <div className={activeTab !== "addresses" ? "hidden md:block" : undefined}>
            <div className="text-theme-secondary-900 dark:text-theme-dark-50 hidden px-6 font-semibold md:mx-auto md:block md:max-w-7xl md:px-10">
                {t("pages.statistics.insights.addresses.title")}
            </div>

            <div>
                <InsightsContainer title={t("pages.statistics.insights.addresses.holdings")} fullWidth>
                    {data.holdings.map((row) => (
                        <div key={row.grouped} className="first:-mt-3 md:first:mt-0">
                            <div className="flex md:hidden">
                                <div className="flex flex-col space-y-2 pt-3">
                                    <span>
                                        &gt; <Number>{row.grouped}</Number> {network.currency}
                                    </span>

                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        <Number>{row.count}</Number>
                                    </span>
                                </div>
                            </div>

                            <div className="hidden w-full justify-between md:flex lg:w-2/3 lg:pr-8 xl:w-1/2 xl:pr-16">
                                <div className="flex flex-1">
                                    <span>
                                        &gt; <Number>{row.grouped}</Number> {network.currency}
                                    </span>
                                </div>
                                <div className="flex flex-1 justify-between">
                                    <span>{t("pages.statistics.insights.addresses.header.addresses")}:</span>
                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        <Number>{row.count}</Number>
                                    </span>
                                </div>
                            </div>
                        </div>
                    ))}
                </InsightsContainer>

                <InsightsContainer title={t("pages.statistics.insights.addresses.unique")} fullWidth>
                    {(["genesis", "newest", "most_transactions", "largest"] as const).map((key) => {
                        const entry = data.unique[key];
                        if (!entry) {
                            return null;
                        }

                        const label = t(`pages.statistics.insights.addresses.header.${key}`);
                        const valueLabel =
                            key === "most_transactions"
                                ? t("pages.statistics.insights.addresses.header.transactions")
                                : key === "largest"
                                  ? t("pages.statistics.insights.addresses.header.balance")
                                  : t("pages.statistics.insights.addresses.header.date");

                        const value =
                            key === "most_transactions" ? (
                                <Number>{entry.value}</Number>
                            ) : key === "largest" ? (
                                networkCurrency(entry.value as number, 2, true)
                            ) : (
                                entry.value
                            );

                        const desktopValue =
                            key === "most_transactions" ? (
                                <Number>{entry.value}</Number>
                            ) : key === "largest" ? (
                                networkCurrency(entry.value as number, 2, true)
                            ) : (
                                entry.value
                            );

                        return (
                            <div key={key} className="first:-mt-3 md:first:mt-0">
                                <div className="flex md:hidden">
                                    <div className="flex w-full flex-col justify-between space-y-3 pt-3 sm:flex-row sm:space-y-0">
                                        <div className="flex flex-col space-y-2">
                                            <span>{label}</span>
                                            <Link href={route("wallet", entry.address)} className="link">
                                                <TruncateMiddle>{entry.address}</TruncateMiddle>
                                            </Link>
                                        </div>

                                        <div className="flex w-[90px] flex-col space-y-2">
                                            <span>{valueLabel}</span>
                                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                {value}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="hidden w-full justify-between md:flex xl:w-[770px]">
                                    <div className="flex flex-1">{label}</div>
                                    <div className="md-lg:flex-2 md-lg:flex-row md-lg:space-y-0 flex flex-1 flex-col justify-between space-y-3">
                                        <div className="flex flex-1 justify-between">
                                            <span>{t("pages.statistics.insights.addresses.header.address")}:</span>
                                            <Link href={route("wallet", entry.address)} className="link">
                                                <TruncateMiddle>{entry.address}</TruncateMiddle>
                                            </Link>
                                        </div>
                                        <div className="md-lg:pl-16 flex w-full flex-1 justify-between space-x-2">
                                            <div>{valueLabel}:</div>
                                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                {desktopValue}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </InsightsContainer>
            </div>
        </div>
    );
}
