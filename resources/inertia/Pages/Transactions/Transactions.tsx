import { Head } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import { usePageMetadata } from "@/Components/General/Metadata";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { TransactionsProps } from "../Transactions.contracts";

export default function Transactions({ network }: PageProps<TransactionsProps>) {
    const { t } = useTranslation();

    const metadata = usePageMetadata({
        page: "transactions",
        detail: {
            name: network.name,
        },
    });

    return (
        <>
            <Head>{metadata}</Head>

            <Layout>
                <PageHeader
                    title={t("pages.transactions.title")}
                    subtitle={t("pages.transactions.subtitle", { network: network.name })}
                />

                {/* Content goes here */}
            </Layout>
        </>
    );
}
