import { useTranslation } from "react-i18next";
import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import { router } from "@inertiajs/react";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { TransactionsProps } from "../Transactions.contracts";
import HeaderStats from "@/Components/Transaction/HeaderStats";
import TransactionsTable from "@/Components/Transaction/TransactionsTable";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { useEffect } from "react";
import useWebhooks from "@/Providers/Webhooks/useWebhooks";

export default function TransactionsList({ network, statistics, filters, transactions }: PageProps<TransactionsProps>) {
    const { t } = useTranslation();
    const { listen } = useWebhooks();

    useEffect(() => {
        return listen('transactions', "NewTransaction", () => {
            router.reload({
                only: ["transactions"],
            });
        });
    }, []);

    return (
        <Layout>
            <PageHeader
                title={t("pages.transactions.title")}
                subtitle={t("pages.transactions.subtitle", { network: network.name })}
            />

            <HeaderStats {...statistics} />

            <PageHandlerProvider>
                <TransactionsTable transactions={transactions} filters={filters} />
            </PageHandlerProvider>
        </Layout>
    );
}
