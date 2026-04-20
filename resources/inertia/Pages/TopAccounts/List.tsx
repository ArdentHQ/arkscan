import { PageProps } from "@inertiajs/core";
import { useTranslation } from "react-i18next";
import Layout from "@/Layout";
import PageHeader from "@/Components/PageHeader/PageHeader";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import TopAccountsTable from "@/Components/Wallet/TopAccountsTable";
import { TopAccountsProps } from "@/Pages/TopAccounts.contracts";

export default function TopAccountsList({ wallets }: PageProps<TopAccountsProps>) {
    const { t } = useTranslation();

    return (
        <Layout>
            <PageHeader title={t("pages.wallets.title")} subtitle={t("pages.wallets.subtitle")} />

            <PageHandlerProvider>
                <TopAccountsTable wallets={wallets} />
            </PageHandlerProvider>
        </Layout>
    );
}
