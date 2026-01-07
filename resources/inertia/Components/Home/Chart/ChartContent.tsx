import { useEffect, useMemo, useRef } from "react";
import { router, usePoll } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import ChartCanvas from "@/Components/Home/Chart/ChartCanvas";
import MarketOverviewHeader from "@/Components/Home/Chart/MarketOverviewHeader";
import MarketStat from "@/Components/Home/Chart/MarketStat";
import { currency, hasSymbol } from "@/utils/number-formatter";
import { HomeChartData, HomeProps } from "@/Pages/Home.contracts";

export default function ChartContent({ chart, canBeExchanged }: { chart: HomeChartData; canBeExchanged: boolean }) {
    const { t } = useTranslation();
    const { broadcasting, network } = useSharedData<HomeProps>();
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
    const volumeValue = chart.market?.volume ?? null;
    const marketCapValue = chart.market?.marketCap ?? null;

    usePoll(
        chart.refreshInterval * 1000,
        {
            only: ["chart"],
        },
        {
            autoStart: broadcasting !== "reverb" && canBeExchanged,
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
        <div className="flex flex-col space-y-4">
            <MarketOverviewHeader showExchanges={canBeExchanged} />

            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
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

            <div className="relative h-[57px] overflow-hidden rounded-lg">
                <div className="absolute inset-0 rounded-lg bg-[radial-gradient(#D5DEE8_1px,transparent_1px)] [background-size:8px_8px] dim:bg-[radial-gradient(#34445C_1px,transparent_1px)] dark:bg-[radial-gradient(#2B3340_1px,transparent_1px)]" />

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
    );
}
