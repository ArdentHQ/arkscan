import { useTranslation } from "react-i18next";
import LearnMore from "../General/LearnMore";
import ArkConnectIcon from "@icons/wallets/arkconnect.svg?react";

export default function ArkConnectLearnMore() {
    const { t } = useTranslation();

    return (
        <LearnMore
            url={t("urls.arkconnect")}
            icon={ArkConnectIcon}
            title={t("pages.compatible-wallets.arkconnect.title")}
            titleExtra={t("pages.compatible-wallets.arkconnect.title_extra")}
            subtitle={t("pages.compatible-wallets.arkconnect.subtitle")}
            backgroundColor={
                "bg-theme-success-50 dark:bg-theme-success-900 border border-transparent dark:border-theme-success-500"
            }
            padding={"p-6 mt-6 sm:py-4"}
            titleColor={"text-theme-secondary-900 dark:text-white"}
            subtitleColor={"text-theme-secondary-700 dark:text-theme-success-700"}
            arrowsClass="arkconnect-arrows"
            iconClass="w-10 h-10 text-[#058751] dark:text-theme-success-600"
        />
    );
}
