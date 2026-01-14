import { useTranslation } from "react-i18next";
import LearnMore from "../General/LearnMore";
import ArkVaultIcon from "@icons/wallets/arkvault.svg?react";

export default function CompatibleWalletsLearnMore() {
    const { t } = useTranslation();

    return (
        <LearnMore
            icon={ArkVaultIcon}
            title={t("brands.arkvault")}
            subtitle={t("pages.compatible-wallets.arkvault.subtitle")}
            backgroundColor={"bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:bg-theme-dim-blue-950"}
            padding={"p-6 sm:p-3 mt-6 lg:px-6"}
            titleColor={"text-theme-secondary-900 dark:text-white"}
            subtitleColor={"text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600"}
            arrowsClass="md-lg:bg-none md-lg:dark:bg-none arkvault-arrows"
        />
    );
}
