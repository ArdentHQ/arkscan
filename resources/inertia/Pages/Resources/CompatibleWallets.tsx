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
import ArkConnectLearnMore from "@/Components/CompatibleWallets/ArkConnectLearnMore";
import MobileDivider from "@/Components/General/MobileDivider";

export default function CompatibleWallets({}: PageProps<HomeProps>) {
    const { t } = useTranslation();

    return (
        <Layout>
            <PageHeader title={t("pages.compatible-wallets.title")} subtitle={t("pages.compatible-wallets.subtitle")} />

            <div className="border-t-4 border-theme-secondary-200 px-6 pb-8 pt-6 dark:border-theme-dark-950 md:mx-auto md:max-w-7xl md:border-0 md:px-10 md:pb-6 md:pt-0">
                <ArkVault />

                <MobileDivider />

                <ArkConnectLearnMore />
            </div>
        </Layout>
    );
}
