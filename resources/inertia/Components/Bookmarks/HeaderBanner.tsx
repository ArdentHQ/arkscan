import classNames from "classnames";
import headerBgImage from "@images/bookmarks/header-bg.svg";
import headerBgImageDark from "@images/bookmarks/header-bg-dark.svg";
import headerBgImageDim from "@images/bookmarks/header-bg-dim.svg";
import { useTranslation } from "react-i18next";

export default function HeaderBanner() {
    const { t } = useTranslation();

    return (
        <div
            className={classNames([
                "relative flex flex-col items-center justify-between rounded sm:flex-row md:rounded-xl",
                "overflow-hidden md:px-6 md:py-3",
                "from-theme-primary-100 to-theme-primary-200 dark:from-theme-dark-700 dark:to-theme-dark-700 bg-gradient-to-r",
            ])}
        >
            <div
                className={classNames(["mx-auto flex flex-1 flex-col items-center bg-right bg-no-repeat md:flex-row"])}
            >
                <div className="md-lg:max-w-[568px] flex flex-col justify-center space-y-1.5 p-4 md:max-w-[356px] md:p-0 xl:max-w-none">
                    <span
                        className={classNames([
                            "space-x-1 text-sm leading-5.25 font-semibold sm:text-lg",
                            "text-theme-primary-900 dark:text-theme-dark-50",
                        ])}
                    >
                        <span>{t("pages.bookmarks.header.title")}</span>
                    </span>

                    <span
                        className={classNames([
                            "text-xs leading-5 font-semibold xl:leading-3.75",
                            "text-theme-secondary-700 dark:text-theme-dark-200",
                        ])}
                    >
                        {t("pages.bookmarks.header.subtitle")}
                    </span>
                </div>

                <div className="relative flex h-30 w-full overflow-hidden md:hidden">
                    <div className="absolute top-1/2 left-1/2 w-[530px] -translate-x-1/2 -translate-y-1/2 sm:w-[690px] md:w-[463px]">
                        <img src={headerBgImage} className="w-full dark:hidden" />
                        <img src={headerBgImageDark} className="dim:hidden hidden w-full dark:block" />
                        <img src={headerBgImageDim} className="dim:block hidden w-full" />
                    </div>
                </div>

                <div className="my-auto hidden w-[530px] sm:w-[690px] md:absolute md:-right-[80px] md:block md:w-[463px]">
                    <img src={headerBgImage} className="w-full dark:hidden" />
                    <img src={headerBgImageDark} className="dim:hidden hidden w-full dark:block" />
                    <img src={headerBgImageDim} className="dim:block hidden w-full" />
                </div>
            </div>
        </div>
    );
}
