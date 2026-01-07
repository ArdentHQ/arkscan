import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";

export default function MarketOverviewHeader({ showExchanges }: { showExchanges: boolean }) {
    const { t } = useTranslation();

    return (
        <div className="pb-3">
            <div className="flex items-center justify-between pb-4">
                <h2 className="mb-0 text-xl font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-2xl">
                    {t("pages.home.statistics.title")}
                </h2>

                {showExchanges && (
                    <Link
                        href={route("exchanges")}
                        className="transition-default flex items-center space-x-1 text-sm font-semibold text-theme-primary-600 hover:text-theme-primary-700 dark:text-theme-dark-blue-400 dark:hover:text-theme-dark-blue-500"
                    >
                        <span>{t("actions.exchanges")}</span>
                        <ChevronRightSmallIcon className="h-3 w-3" />
                    </Link>
                )}
            </div>
            <hr className="-mx-6 border-b border-t-0 border-theme-secondary-300 dark:border-theme-dark-700" />
        </div>
    );
}
