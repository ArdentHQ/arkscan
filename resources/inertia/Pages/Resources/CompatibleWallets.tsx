import Layout from "@/Layout";
import { PageProps } from "@inertiajs/core";
import PageHeader from "@/Components/PageHeader/PageHeader";
import { useTranslation } from "react-i18next";
import ArkVault from "@/Components/CompatibleWallets/ArkVault";
import ArkConnectLearnMore from "@/Components/CompatibleWallets/ArkConnectLearnMore";
import MobileDivider from "@/Components/General/MobileDivider";
import WalletGrid from "@/Components/CompatibleWallets/WalletGrid";
import { CompatibleWalletsProps } from "../CompatibleWallets.contracts";
import CompatibleWalletsSubmitCTA from "@/Components/CompatibleWallets/SubmitCTA";

export default function CompatibleWallets({ wallets }: PageProps<CompatibleWalletsProps>) {
    const { t } = useTranslation();

    return (
        <Layout className="pb-6 pt-8">
            <PageHeader title={t("pages.compatible-wallets.title")} subtitle={t("pages.compatible-wallets.subtitle")} />

            <div className="border-t-4 border-theme-secondary-200 px-6 pt-6 dark:border-theme-dark-950 md:mx-auto md:max-w-7xl md:border-0 md:px-10 md:pt-0">
                <ArkVault />

                <MobileDivider />

                <ArkConnectLearnMore />

                <MobileDivider />

                <WalletGrid wallets={wallets} />

                <CompatibleWalletsSubmitCTA />
            </div>
        </Layout>
    );
}
