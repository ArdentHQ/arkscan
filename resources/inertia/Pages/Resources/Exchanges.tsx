import Layout from "@/Layout";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { useTranslation } from "react-i18next";
import ExchangesTableWrapper from "@/Components/Tables/Desktop/Exchanges/Exchanges";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import ExchangesMobileTableWrapper from "@/Components/Tables/Mobile/Exchanges/Exchanges";
import { router } from "@inertiajs/react";
import { useEffect } from "react";
import ExchangeTableFilters from "@/Components/Exchanges/TableFilters";
import ExchangesSubmitCTA from "@/Components/Exchanges/SubmitCTA";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import ExchangesChart from "@/Components/Exchanges/Chart";
import MobileDivider from "@/Components/General/MobileDivider";
import useSharedData from "@/hooks/use-shared-data";
import { ExchangesProps } from "@/Pages/Exchanges.contracts";

function TableWrapper() {
    const { setRefreshPage } = usePageHandler();

    const updateTable = (callback?: CallableFunction) => {
        router.reload({
            only: ["exchanges"],
            onSuccess: () => {
                if (callback) {
                    callback();
                }
            },
        });
    };

    useEffect(() => {
        updateTable();

        setRefreshPage((callback: CallableFunction) => {
            updateTable(callback);
        });
    }, []);

    return <ExchangesTableWrapper mobile={<ExchangesMobileTableWrapper />} />;
}

export default function Exchanges() {
    const { t } = useTranslation();
    const { chart, network } = useSharedData<ExchangesProps>();

    return (
        <Layout className="pb-8 pt-8 md:pb-6">
            {chart && network?.canBeExchanged && (
                <>
                    <div className="hidden flex-col px-6 sm:flex md:mx-auto md:max-w-7xl md:px-10">
                        <div className="text-lg font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-2xl">
                            {t("pages.exchanges.live_price_chart")}
                        </div>

                        <ExchangesChart chart={chart} />
                    </div>

                    <MobileDivider className="my-6 hidden sm:block" />

                    <div className="hidden flex-col px-6 sm:flex md:mx-auto md:max-w-7xl md:px-10">
                        <hr className="my-8 hidden h-px text-theme-secondary-300 dark:text-theme-dark-700 md:block" />
                    </div>
                </>
            )}

            <PageHeader
                title={t("pages.exchanges.title")}
                subtitle={t("pages.exchanges.subtitle")}
                right={<ExchangeTableFilters />}
            />

            <PageHandlerProvider>
                <TableWrapper />
            </PageHandlerProvider>

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <ExchangesSubmitCTA />
            </div>
        </Layout>
    );
}
