import classNames from "classnames";
import { useEffect, useMemo, useRef } from "react";
import { router, usePoll } from "@inertiajs/react";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import ChartCanvas from "@/Components/Home/Chart/ChartCanvas";
import ExchangesButton from "@/Components/Home/Chart/ExchangesButton";
import PeriodDropdown from "@/Components/Home/Chart/PeriodDropdown";
import PriceTicker from "@/Components/Home/Chart/PriceTicker";
import { HomeChartPeriod, HomeProps } from "@/Pages/Home.contracts";

const DEFAULT_PERIOD: HomeChartPeriod = "day";

export default function ChartContent() {
    const {
        broadcasting,
        chart,
        network: { canBeExchanged = false },
    } = useSharedData<HomeProps>();
    const { currency, theme } = useSettings();
    const previousCurrencyRef = useRef(currency);

    const chartTheme = useMemo(() => ({ ...chart.theme, mode: theme }), [chart.theme, theme]);

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
        if (previousCurrencyRef.current === currency) {
            return;
        }

        previousCurrencyRef.current = currency;

        router.reload({
            only: ["chart"],
            showProgress: false,
        });
    }, [currency]);

    const updatePeriod = (period: HomeChartPeriod) => {
        if (period === chart.period) {
            return;
        }

        const url = new URL(window.location.href);

        if (period === DEFAULT_PERIOD) {
            url.searchParams.delete("chartPeriod");
        } else {
            url.searchParams.set("chartPeriod", period);
        }

        router.get(
            url.toString(),
            {},
            {
                preserveScroll: true,
                preserveState: true,
                replace: true,
                showProgress: false,
                only: ["chart"],
            },
        );
    };

    return (
        <div className="h-full flex-col">
            <div
                className={classNames("h-full flex-row justify-between sm:flex-col", {
                    "hidden md-lg:flex": !canBeExchanged,
                    flex: canBeExchanged,
                })}
            >
                <div className="flex items-center whitespace-nowrap sm:flex-1 sm:justify-between">
                    <PriceTicker />

                    <div className="hidden items-center space-x-3 sm:flex">
                        <PeriodDropdown period={chart.period} onChange={updatePeriod} />
                        <ExchangesButton />
                    </div>
                </div>

                <div className="flex min-w-0 flex-1 justify-end sm:items-center sm:justify-between">
                    <div className="hidden h-[140px] w-full sm:mt-4 sm:flex md:h-[157px] lg:mt-[1.125rem]">
                        <ChartCanvas
                            id="price-chart"
                            className="h-auto w-full"
                            canvasClassName="max-w-full"
                            datasets={chart.datasets}
                            labels={chart.labels}
                            theme={chartTheme}
                            currency={currency}
                            height={109}
                            width={null}
                            grid
                            tooltips
                            showCrosshair
                            hasDateTimeLabels
                            yPadding={10}
                            xPadding={0}
                        />
                    </div>

                    <ExchangesButton className="hidden items-center xs:flex sm:hidden" />
                </div>
            </div>

            <ExchangesButton className="mt-3 flex items-center xs:hidden" buttonClassName="flex-1" />
        </div>
    );
}
