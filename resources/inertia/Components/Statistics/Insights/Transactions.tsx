import { useTranslation } from "react-i18next";
import Number from "@/Components/General/Number";
import InsightsContainer from "./Container";
import InsightsRow from "./Row";
import { StatisticsTransactionInsights } from "@/Pages/Statistics.contracts";
import { networkCurrency } from "@/utils/number-formatter";
import TransactionRecordDesktopRow from "./TransactionRecordDesktopRow";
import TransactionRecordMobileRow from "./TransactionRecordMobileRow";

export default function TransactionInsights({
    data,
    activeTab,
}: {
    data: StatisticsTransactionInsights;
    activeTab: string;
}) {
    const { t } = useTranslation();

    return (
        <div className={activeTab !== "transactions" ? "hidden md:block" : undefined}>
            <div className="text-theme-secondary-900 dark:text-theme-dark-50 hidden px-6 font-semibold md:mx-auto md:block md:max-w-7xl md:px-10">
                {t("pages.statistics.insights.transactions.title")}
            </div>

            <div>
                <InsightsContainer title={t("pages.statistics.insights.transactions.all_time")}>
                    {Object.entries(data.details).map(([key, detail]) => (
                        <InsightsRow key={key} title={t(`pages.statistics.insights.transactions.header.${key}`)}>
                            <Number>{detail}</Number>
                        </InsightsRow>
                    ))}
                </InsightsContainer>

                <InsightsContainer title={t("pages.statistics.insights.transactions.daily_averages")}>
                    {(["transactions", "transaction_volume", "transaction_fees"] as const).map((key) => {
                        const value = data.averages[key];
                        const content =
                            key === "transactions" ? (
                                <Number>{value}</Number>
                            ) : (
                                networkCurrency(value as number, 8, true)
                            );

                        return (
                            <InsightsRow key={key} title={t(`pages.statistics.insights.transactions.header.${key}`)}>
                                {content}
                            </InsightsRow>
                        );
                    })}
                </InsightsContainer>

                <InsightsContainer title={t("pages.statistics.insights.transactions.records")} fullWidth>
                    {Object.entries(data.records).map(([key, record]) => (
                        <div key={key} className="first:-mt-3 md:first:mt-0">
                            <TransactionRecordMobileRow recordKey={key} record={record} />
                            <TransactionRecordDesktopRow recordKey={key} record={record} />
                        </div>
                    ))}
                </InsightsContainer>
            </div>
        </div>
    );
}
