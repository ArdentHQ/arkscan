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

    return (
        <Layout className="pb-6 pt-8">
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
