import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";

export default function MarketOverviewHeader({ showExchanges }: { showExchanges: boolean }) {
    const { t } = useTranslation();

    return (
        <div>
            <div className="flex items-center justify-between pb-4">
                <h2 className="mb-0 text-xl font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-2xl">
                    {t("pages.home.statistics.title")}
                </h2>

                {showExchanges && (
                    <Link
                        href={route("exchanges")}
                        className="link transition-default flex items-center space-x-2 font-semibold"
                    >
                        <span>{t("actions.exchanges")}</span>
                        <ChevronRightSmallIcon className="h-3 w-3" />
                    </Link>
                )}
            </div>
            <hr className="-mx-4 border-b border-t-0 border-theme-secondary-300 dark:border-theme-dark-700 sm:-mx-6" />
        </div>
    );
}
