import { useEffect, useMemo, useRef } from "react";
import { Link, router, usePoll } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import ChartCanvas from "@/Components/Home/Chart/ChartCanvas";
import MarketOverviewHeader from "@/Components/Home/Chart/MarketOverviewHeader";
import MarketStat from "@/Components/Home/Chart/MarketStat";
import { currency, currencyWithDecimals, hasSymbol } from "@/utils/number-formatter";
import { HomeChartData, HomeProps } from "@/Pages/Home.contracts";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";

export default function ChartContent({ chart, canBeExchanged }: { chart: HomeChartData; canBeExchanged: boolean }) {
    const { t } = useTranslation();
    const { usesBroadcasting, network } = useSharedData<HomeProps>();
    const { currency: selectedCurrency, theme, isPriceAvailable, priceExchangeRate } = useSettings();
    const previousCurrencyRef = useRef(selectedCurrency);

    const chartTheme = useMemo(() => ({ ...chart.theme, mode: theme }), [chart.theme, theme]);
    const currencySuffix = hasSymbol(selectedCurrency) ? selectedCurrency : undefined;
    const priceValue =
        isPriceAvailable && priceExchangeRate !== null ? currency(priceExchangeRate, selectedCurrency) : null;
    const pricePair =
        network?.currency && network.currency !== selectedCurrency
            ? `${network.currency}/${selectedCurrency}`
            : undefined;
    const priceTitle = network?.currency
        ? `${network.currency} ${t("pages.home.charts.price")}`
        : t("pages.home.charts.price");
    const volumeValue =
        chart.market?.volume !== null && chart.market?.volume !== undefined
            ? currencyWithDecimals({ value: chart.market.volume, currency: selectedCurrency, decimals: 0 })
            : null;
    const marketCapValue =
        chart.market?.marketCap !== null && chart.market?.marketCap !== undefined
            ? currencyWithDecimals({ value: chart.market.marketCap, currency: selectedCurrency, decimals: 0 })
            : null;
    const dotsClassName =
        chartTheme.name === "red"
            ? "bg-[radial-gradient(var(--theme-color-danger-100)_1px,transparent_1px)] dark:bg-[radial-gradient(var(--theme-color-dark-800)_1px,transparent_1px)] dim:bg-[radial-gradient(var(--theme-color-dim-800)_1px,transparent_1px)]"
            : "bg-[radial-gradient(var(--theme-color-success-100)_1px,transparent_1px)] dark:bg-[radial-gradient(var(--theme-color-success-800)_1px,transparent_1px)] dim:bg-[radial-gradient(var(--theme-color-dim-800)_1px,transparent_1px)]";

    usePoll(
        chart.refreshInterval * 1000,
        {
            only: ["chart"],
        },
        {
            autoStart: !usesBroadcasting && canBeExchanged,
        },
    );

    useEffect(() => {
        if (previousCurrencyRef.current === selectedCurrency) {
            return;
        }

        previousCurrencyRef.current = selectedCurrency;

        router.reload({
            only: ["chart"],
            showProgress: false,
        });
    }, [selectedCurrency]);

    return (
        <div className="flex flex-col px-4 pb-3 sm:px-0 sm:pb-0">
            <div className="flex items-end justify-between sm:hidden sm:items-center">
                <div className="flex flex-col space-y-2">
                    <div className="text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                        {priceTitle}
                    </div>
                    <div className="text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                        {priceValue ?? t("general.na")}
                        {currencySuffix && priceValue && <span className="ml-1">{currencySuffix}</span>}
                    </div>
                </div>

                {canBeExchanged && (
                    <Link
                        href={route("exchanges")}
                        className="link flex items-center space-x-2 rounded px-2 py-1.5 font-semibold hover:bg-theme-primary-200 hover:text-theme-primary-700 dark:hover:bg-theme-dark-700 dark:hover:text-theme-dark-50"
                    >
                        <span>{t("actions.exchanges")}</span>

                        <ChevronRightSmallIcon className="h-3 w-3" />
                    </Link>
                )}
            </div>

            <div className="hidden flex-col sm:flex">
                <MarketOverviewHeader showExchanges={canBeExchanged} />

                <div className="px-4 md-lg:px-6">
                    <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <MarketStat
                            label={t("pages.home.charts.price")}
                            value={priceValue}
                            suffix={pricePair}
                            disabled={!canBeExchanged || !isPriceAvailable || priceValue === null}
                        />

                        <MarketStat
                            label={t("pages.home.statistics.volume")}
                            value={volumeValue}
                            suffix={currencySuffix}
                            disabled={!canBeExchanged || volumeValue === null}
                        />

                        <MarketStat
                            label={t("pages.home.statistics.market_cap")}
                            value={marketCapValue}
                            suffix={currencySuffix}
                            disabled={!canBeExchanged || marketCapValue === null}
                        />
                    </div>

                    <div className="relative mt-3 h-[57px] overflow-hidden rounded-lg">
                        <div className={`absolute inset-0 rounded-lg [background-size:6px_6px] ${dotsClassName}`} />

                        <div className="relative z-10 h-full">
                            <ChartCanvas
                                id="price-chart"
                                className="h-full w-full"
                                canvasClassName="max-w-full"
                                datasets={chart.datasets}
                                labels={chart.labels}
                                theme={chartTheme}
                                currency={selectedCurrency}
                                grid={false}
                                tooltips={false}
                                showCrosshair={false}
                                hasDateTimeLabels
                                height={57}
                                yPadding={0}
                                xPadding={0}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
