import classNames from "classnames";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import ChartContent from "@/Components/Home/Chart/ChartContent";
import { HomeProps } from "@/Pages/Home.contracts";

export default function ChartCard() {
    const { t } = useTranslation();
    const {
        chart,
        network: { canBeExchanged = false },
    } = useSharedData<HomeProps>();

    if (!chart) {
        return null;
    }

    return (
        <div
            className={classNames(
                "flex-1 rounded-xl border border-theme-secondary-300 bg-white px-4 py-3 dark:border-theme-dark-700 dark:bg-theme-dark-950 sm:px-6 sm:py-4",
                {
                    "md-lg:py-6": !canBeExchanged,
                },
            )}
        >
            <div
                className={classNames("relative h-full w-full", {
                    "hidden md-lg:block": !canBeExchanged,
                })}
            >
                {!canBeExchanged && (
                    <div className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-400">
                        {t("pages.home.statistics.chart_not_supported")}
                    </div>
                )}

                <div
                    className={classNames({
                        "pointer-events-none blur-md": !canBeExchanged,
                    })}
                >
                    <ChartContent chart={chart} canBeExchanged={canBeExchanged} />
                </div>
            </div>
        </div>
    );
}
