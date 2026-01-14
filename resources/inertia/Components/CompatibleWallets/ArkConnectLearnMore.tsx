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
            iconClass="w-11 h-11 text-[#058751] dark:text-theme-success-600"
        />
    );
}

// @props([
//     'backgroundColor' => 'bg-theme-success-50 dark:bg-theme-success-900 border border-transparent dark:border-theme-success-500',
//     'padding' => 'p-6 mt-6 sm:py-4',
//     'titleColor' => 'text-theme-secondary-900 dark:text-white',
//     'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-success-700',
//     'iconSize' => 'w-10 h-10',
//     'buttonColor' => '!bg-theme-success-600 hover:!bg-theme-success-700',
// ])

// <x-general.learn-more
//     :url="trans('urls.arkconnect')"
//     icon="app-wallets.arkconnect"
//     icon-color="text-[#058751] dark:text-theme-success-600"
//     :title="trans('pages.compatible-wallets.arkconnect.title')"
//     :title-extra="trans('pages.compatible-wallets.arkconnect.title_extra')"
//     :subtitle="trans('pages.compatible-wallets.arkconnect.subtitle')"
//     :background-color="$backgroundColor"
//     :padding="$padding"
//     :title-color="$titleColor"
//     :subtitle-color="$subtitleColor"
//     :icon-size="$iconSize"
//     :button-color="$buttonColor"
//     arrows-class="arkconnect-arrows"
//     mobile-tall
// />
