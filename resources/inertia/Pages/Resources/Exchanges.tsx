import Layout from "@/Layout";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { useTranslation } from "react-i18next";
import ExchangesTableWrapper from "@/Components/Tables/Desktop/Exchanges/Exchanges";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import ExchangesMobileTableWrapper from "@/Components/Tables/Mobile/Exchanges/Exchanges";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { router } from "@inertiajs/react";
import { useEffect } from "react";
import ExchangeTableFilters from "@/Components/Exchanges/TableFilters";
import ExchangesSubmitCTA from "@/Components/Exchanges/SubmitCTA";
import { ExchangesProps } from "../Exchanges.contracts";

function TableWrapper({ exchanges }: Pick<ExchangesProps, "exchanges">) {
    const updateTable = () => {
        // setTimeout(() => {
        router.reload({
            only: ["exchanges"],
        });
        // }, 100);
    };

    useEffect(() => {
        updateTable();
    }, []);

    return <ExchangesTableWrapper exchanges={exchanges} mobile={<ExchangesMobileTableWrapper />} />;
}

export default function Exchanges({ exchanges }: ExchangesProps) {
    const { t } = useTranslation();

    console.log({ exchanges });

    return (
        <Layout className="pb-6 pt-8">
            {/* <PageHeader
                title={t("pages.exchanges.title")}
                subtitle={t("pages.exchanges.subtitle")}
                right={<ExchangeTableFilters />}
            /> */}

            <PageHandlerProvider>
                <TableWrapper exchanges={exchanges} />
            </PageHandlerProvider>

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">{/* <ExchangesSubmitCTA /> */}</div>
        </Layout>
    );
}
