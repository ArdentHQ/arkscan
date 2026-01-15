import { useEffect, useRef } from "react";
import { PageProps } from "@inertiajs/core";
import { router, usePoll } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import Layout from "@/Layout";
import PageHeader from "@/Components/PageHeader/PageHeader";
import GasTracker from "@/Components/Statistics/GasTracker";
import Highlights from "@/Components/Statistics/Highlights";
import InformationCards from "@/Components/Statistics/InformationCards";
import Insights from "@/Components/Statistics/Insights/Insights";
import { StatisticsProps } from "@/Pages/Statistics.contracts";
import useSettings from "@/Providers/Settings/useSettings";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";

export default function StatisticsIndex({
    refreshInterval,
    gasTracker,
    highlights,
    informationCards,
    insights,
}: PageProps<StatisticsProps>) {
    const { t } = useTranslation();
    const { currency } = useSettings();
    const previousCurrencyRef = useRef(currency);
    const { listen } = useWebhooks();

    usePoll(refreshInterval * 1000, {
        only: ["gasTracker", "highlights", "informationCards"],
    });

    useEffect(() => {
        if (previousCurrencyRef.current === currency) {
            return;
        }

        previousCurrencyRef.current = currency;

        router.reload({
            only: ["gasTracker", "informationCards", "insights"],
            showProgress: false,
        });
    }, [currency]);

    useEffect(() => {
        const reloadInsights = () => {
            router.reload({
                only: ["insights"],
                showProgress: false,
            });
        };

        const handlers = [
            listen("statistics-update", "Statistics\\TransactionDetails", reloadInsights),
            listen("statistics-update", "Statistics\\MarketData", reloadInsights),
            listen("statistics-update", "Statistics\\ValidatorDetails", reloadInsights),
            listen("statistics-update", "Statistics\\AddressHoldings", reloadInsights),
            listen("statistics-update", "Statistics\\UniqueAddresses", reloadInsights),
            listen("statistics-update", "Statistics\\AnnualData", reloadInsights),
        ];

        return () => {
            handlers.forEach((handler) => handler?.());
        };
    }, [listen]);

    return (
        <Layout>
            <PageHeader title={t("pages.statistics.title")} subtitle={t("pages.statistics.subtitle")} />

            <div className="pb-6 md:mx-auto md:max-w-7xl md:px-10">
                <GasTracker data={gasTracker} />
            </div>

            <Highlights data={highlights} />

            <div>
                <div className="space-y-6 border-t-4 border-theme-secondary-200 px-6 py-6 dark:border-theme-dark-950 md:mx-auto md:max-w-7xl md:border-0 md:px-10">
                    <InformationCards data={informationCards} />
                </div>
            </div>

            <Insights data={insights} />
        </Layout>
    );
}
