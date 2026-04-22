import { useTranslation } from "react-i18next";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import { Link } from "@inertiajs/react";

export default function MarketOverviewHeader({ showExchanges }: { showExchanges: boolean }) {
    const { t } = useTranslation();

    return (
        <div className="border-theme-secondary-300 dark:border-theme-dark-700 mb-3 flex items-center justify-between border-b px-4 pb-3 sm:px-6 md:mb-4 md:pb-4">
            <h2 className="mb-0 text-lg leading-5.25 font-semibold md:text-2xl">
                {t("pages.home.statistics.title_market_overview")}
            </h2>

            {showExchanges && (
                <Link
                    href={route("exchanges")}
                    className="link hover:bg-theme-primary-200 hover:text-theme-primary-700 hover:dark:bg-theme-dark-700 hover:dark:text-theme-dark-50! rounded px-2 py-1.5 font-semibold"
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
