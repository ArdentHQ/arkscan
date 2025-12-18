import { Head } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { TransactionsProps } from "../Transactions.contracts";
import HeaderStats from "@/Components/Transaction/HeaderStats";
import TransactionsTable from "@/Components/Transaction/TransactionsTable";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { useEffect } from "react";
import { router } from "@inertiajs/react";

export default function Transactions({ network, statistics, filters, transactions }: PageProps<TransactionsProps>) {
    const { t } = useTranslation();

    const metadata = usePageMetadata({
        page: "transactions",
        detail: {
            name: network.name,
        },
    });

    useEffect(() => {
        router.reload({
            only: ["transactions"],
        });
    }, []);

    return (
        <>
            <Head>{metadata}</Head>

            <Layout>
                <PageHeader
                    title={t("pages.transactions.title")}
                    subtitle={t("pages.transactions.subtitle", { network: network.name })}
                />

                <HeaderStats {...statistics} />

                <TransactionsTable transactions={transactions} filters={filters} />
            </Layout>
        </>
    );
}
