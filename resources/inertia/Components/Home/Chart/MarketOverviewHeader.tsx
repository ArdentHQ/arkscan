import { useTranslation } from "react-i18next";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import { Link } from "@inertiajs/react";

export default function MarketOverviewHeader({ showExchanges }: { showExchanges: boolean }) {
    const { t } = useTranslation();

    return (
        <div className="mb-3 flex items-center justify-between border-b border-theme-secondary-300 px-4 pb-3 dark:border-theme-dark-700 sm:px-6 md:mb-4 md:pb-4">
            <h2 className="mb-0 text-lg font-semibold leading-5.25 md:text-2xl">
                {t("pages.home.statistics.title_market_overview")}
            </h2>

            {showExchanges && (
                <Link
                    href={route("exchanges")}
                    className="link rounded px-2 py-1.5 font-semibold hover:bg-theme-primary-200 hover:text-theme-primary-700 dark:hover:bg-theme-dark-700 dark:hover:text-theme-dark-50"
                >
                    <div className="inline-flex items-center space-x-2">
                        <span className="leading-5">{t("actions.exchanges")}</span>

                        <ChevronRightSmallIcon className="h-3 w-3" />
                    </div>
                </Link>
            )}
        </div>
    );
}
