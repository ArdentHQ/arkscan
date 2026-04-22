import { useTranslation } from "react-i18next";
import HeaderItem from "@/Components/PageHeader/HeaderItem";
import ArkVaultIcon from "@icons/wallets/arkvault.svg?react";
import footerBg from "@images/home/footer-bg.svg";
import footerBgDark from "@images/home/footer-bg-dark.svg";
import footerBgDim from "@images/home/footer-bg-dim.svg";
import footerImage from "@images/home/footer.svg";
import footerImageDark from "@images/home/footer-dark.svg";
import footerImageDim from "@images/home/footer-dim.svg";
import useSharedData from "@/hooks/use-shared-data";

export default function ArkVaultCTA() {
    const { t } = useTranslation();
    const { urls } = useSharedData();

    return (
        <HeaderItem
            className="-mx-6 -mb-8 mt-8 flex-none rounded-none bg-theme-primary-100 dim:bg-theme-dark-700 dark:bg-theme-dark-800 sm:h-[140px] md:mx-0 md:mb-0 md:mt-6 md:h-[150px] md-lg:h-[193px] lg:h-[206px] xl:h-[264px] xl:flex-1"
            contentClass="p-6 h-full sm:py-0"
            slotClass="h-full"
            withoutPadding
            backgroundClass="left-0 right-auto max-w-none sm:left-auto sm:right-0"
            background={
                <>
                    <img src={footerBg} className="max-w-none dark:hidden sm:block" alt="" />

                    <img src={footerBgDark} className="hidden max-w-none dim:hidden dark:block" alt="" />

                    <img src={footerBgDim} className="hidden max-w-none dim:block" alt="" />
                </>
            }
        >
            <div className="absolute right-0 top-0 z-10 h-full w-full bg-gradient-to-t from-theme-primary-100 to-theme-primary-200 dim:bg-gradient-to-b dark:from-theme-dark-800 dark:to-theme-dark-700 sm:w-3/4 sm:bg-gradient-to-r dim:sm:bg-gradient-to-l"></div>

            <div className="relative z-30 flex h-full flex-1 flex-col items-center sm:flex-row sm:justify-between">
                <div className="-ml-24 hidden h-full sm:block md:-ml-16 lg:ml-0">
                    <img src={footerImage} className="h-full dark:hidden" alt="" />
                    <img src={footerImageDark} className="hidden h-full dim:hidden dark:block" alt="" />
                    <img src={footerImageDim} className="hidden h-full dim:block" alt="" />
                </div>

                <div className="flex w-full flex-1 flex-col sm:ml-6 sm:w-auto md:ml-2 md-lg:pl-8 lg:ml-6">
                    <div className="hidden md-lg:block">
                        <div className="text-2xl font-semibold leading-[29px] text-theme-primary-900 dark:text-theme-dark-50 lg:text-3xl lg:font-bold lg:leading-10">
                            {t("pages.home.footer.title")}
                        </div>

                        <div className="mt-2 text-sm font-semibold text-theme-secondary-800 dark:text-theme-dark-200 lg:text-base lg:leading-5">
                            {t("pages.home.footer.subtitle")}
                        </div>
                    </div>

                    <div className="flex flex-col justify-between rounded-xl border border-theme-primary-300 bg-[#F5FAFF]/30 p-6 backdrop-blur dim:bg-[#476DB0]/30 dark:border-theme-dark-500 dark:bg-[#505D6A]/30 sm:flex-row sm:p-3 md-lg:mt-5 lg:px-6">
                        <div className="arkvault-arrows-home mx-auto flex flex-1 items-center bg-right bg-no-repeat sm:ml-0 sm:mr-2 md-lg:bg-none md-lg:dark:bg-none">
                            <div>
                                <ArkVaultIcon className="h-11 w-11 text-theme-navy-600 dark:text-white" />
                            </div>

                            <div className="ml-3 flex flex-col justify-center space-y-2">
                                <span className="text-lg font-semibold leading-6 text-theme-primary-900 dark:text-theme-dark-50">
                                    {t("brands.arkvault")}
                                </span>

                                <span className="text-xs font-semibold leading-3.75 text-theme-secondary-800 dark:text-theme-dark-200">
                                    {t("pages.compatible-wallets.arkvault.subtitle")}
                                </span>
                            </div>
                        </div>

                        <div className="mt-4 flex items-center sm:mt-0 sm:h-auto">
                            <a
                                href={urls.arkvault}
                                target="_blank"
                                rel="noopener nofollow noreferrer"
                                className="button-primary flex w-full items-center rounded-lg py-3.5 dim:bg-theme-dark-blue-600! dim:hover:bg-theme-dark-blue-700! dark:bg-theme-dark-blue-500! dark:hover:bg-theme-dark-blue-600! sm:mt-0 sm:h-15 sm:w-auto md:mt-0 md:w-full lg:w-auto"
                            >
                                <div className="flex h-full items-center justify-center text-lg leading-5.25">
                                    <span>{t("actions.learn_more")}</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </HeaderItem>
    );
}
