import ArkVaultCTA from "@/Components/Home/ArkVaultCTA";
import { HomeProps } from "@/Pages/Home.contracts";
import Layout from "@/Layout";
import PageHandlerProvider from "@/Providers/PageHandler/PageHandlerProvider";
import { PageProps } from "@inertiajs/core";
import Statistics from "@/Components/Home/Statistics";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { useTranslation } from "react-i18next";
import Card from "@/Components/General/Card";
import ArkVault from "@/Components/CompatibleWallets/ArkVault";

export default function CompatibleWallets({}: PageProps<HomeProps>) {
    const { t } = useTranslation();

    return (
        <Layout>
            <PageHeader title={t("pages.compatible-wallets.title")} subtitle={t("pages.compatible-wallets.subtitle")} />

            <div className="mx-auto flex max-w-7xl flex-col dark:text-theme-dark-200 md:px-10 lg:flex-row">
                <ArkVault />
            </div>
        </Layout>
    );
}
