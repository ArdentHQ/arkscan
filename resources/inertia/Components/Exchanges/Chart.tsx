import { useEffect, useMemo, useRef, useState } from "react";
import { Link, router, usePoll } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import classNames from "classnames";
import Card from "@/Components/General/Card";
import ChartCanvas from "@/Components/Home/Chart/ChartCanvas";
import Percentage from "@/Components/General/Percentage";
import Select from "@/Components/General/Select";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import { currency, currencyWithDecimals, hasSymbol } from "@/utils/number-formatter";
import {
    ExchangeChartData,
    ExchangeChartOption,
    ExchangeChartPeriod,
    ExchangesProps,
} from "@/Pages/Exchanges.contracts";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";

function PeriodSelect({
    options,
    selected,
    onSelect,
}: {
    options: ExchangeChartOption[];
    selected: ExchangeChartPeriod;
    onSelect: (value: ExchangeChartPeriod) => void;
}) {
    const selectedOption = options.find((option) => option.value === selected);

    return (
        <div className="w-full sm:w-[150px]">
            <Select value={selected} onValueChange={(value) => onSelect(value as ExchangeChartPeriod)}>
                <Select.Trigger
                    className="form-input transition-default dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:text-theme-dark-200 h-10 w-full bg-white px-3! py-2! text-left text-sm! font-semibold"
                    placeholder={selectedOption?.label}
                />
                <Select.Content
                    align="start"
                    sideOffset={4}
                    className="mt-1 w-[var(--radix-select-trigger-width)] origin-top-left"
                >
                    {options.map((option) => (
                        <Select.Item key={option.value} value={option.value}>
                            {option.label}
                        </Select.Item>
                    ))}
                </Select.Content>
            </Select>
        </div>
    );
}

function PeriodTabs({
    options,
    selected,
    onSelect,
}: {
    options: ExchangeChartOption[];
    selected: ExchangeChartPeriod;
    onSelect: (value: ExchangeChartPeriod) => void;
}) {
    return (
        <div className="relative z-0 inline-flex">
            <div className="bg-theme-secondary-200 dark:bg-theme-dark-950 relative z-10 inline-flex items-center justify-between rounded-xl sm:p-1">
                <div role="tablist" className="flex space-x-1 px-0! pr-6 sm:pr-0">
                    {options.map((option) => {
                        const isSelected = selected === option.value;

                        return (
                            <button
                                key={option.value}
                                type="button"
                                role="tab"
                                aria-selected={isSelected}
                                tabIndex={isSelected ? 0 : -1}
                                className="group/tab transition-default text-theme-secondary-700 hover:text-theme-secondary-900 dark:text-theme-dark-200 dark:hover:text-theme-secondary-200 relative flex cursor-pointer items-center"
                                onClick={() => onSelect(option.value)}
                            >
                                <span
                                    className={classNames(
                                        "transition-default block h-full w-full rounded font-semibold whitespace-nowrap sm:rounded-lg",
                                        "px-3 py-1.5",
                                        isSelected &&
                                            "text-theme-secondary-900 dark:bg-theme-dark-800 dark:text-theme-dark-50 bg-white",
                                        !isSelected &&
                                            "group-hover/tab:bg-theme-secondary-300 group-hover/tab:text-theme-secondary-900 dark:group-hover/tab:bg-theme-dark-900 dark:group-hover/tab:text-theme-dark-50",
                                    )}
                                >
                                    {option.label}
                                </span>
                            </button>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}

export default function ExchangesChart({ chart }: { chart: ExchangeChartData }) {
    const { t } = useTranslation();
    const { usesBroadcasting, network } = useSharedData<ExchangesProps>();
    const { currency: selectedCurrency, theme } = useSettings();
    const previousCurrencyRef = useRef(selectedCurrency);
    const [selectedPeriod, setSelectedPeriod] = useState<ExchangeChartPeriod>(chart.period);
    const selectedPeriodRef = useRef<ExchangeChartPeriod>(chart.period);

    useEffect(() => {
        selectedPeriodRef.current = selectedPeriod;
    }, [selectedPeriod]);

    useEffect(() => {
        setSelectedPeriod(chart.period);
    }, [chart.period]);

    const chartTheme = useMemo(() => ({ ...chart.theme, mode: theme }), [chart.theme, theme]);
    const currencySuffix = hasSymbol(selectedCurrency) ? selectedCurrency : null;

    const mainValueFormatted = currency(chart.mainValueFiat, selectedCurrency);
    const marketCapFormatted =
        chart.marketCapValue !== null
            ? currencyWithDecimals({ value: chart.marketCapValue, currency: selectedCurrency, decimals: 0 })
            : null;
    const minPriceFormatted = currency(chart.minPriceValue, selectedCurrency);
    const maxPriceFormatted = currency(chart.maxPriceValue, selectedCurrency);

    const updatePeriod = (value: ExchangeChartPeriod) => {
        if (value === selectedPeriod) {
            return;
        }

        setSelectedPeriod(value);

        router.reload({
            only: ["chart"],
            data: {
                chartPeriod: value,
            },
            showProgress: false,
        });
    };

    usePoll(
        chart.refreshInterval * 1000,
        {
            only: ["chart"],
            data: {
                chartPeriod: selectedPeriod,
            },
        },
        {
            autoStart: !usesBroadcasting && (network?.canBeExchanged ?? false),
        },
    );

    useEffect(() => {
        if (previousCurrencyRef.current === selectedCurrency) {
            return;
        }

        previousCurrencyRef.current = selectedCurrency;

        router.reload({
            only: ["chart"],
            data: {
                chartPeriod: selectedPeriodRef.current,
            },
            showProgress: false,
        });
    }, [selectedCurrency]);

    if (!network?.canBeExchanged) {
        return null;
    }

    return (
        <div className="mt-2 space-y-2 md:mt-6" data-testid="exchanges:chart">
            <Card className="flex flex-col py-4 md:pb-6">
                <div className="flex flex-col space-y-3 border-x border-t border-transparent sm:flex-row sm:justify-between sm:space-y-0">
                    <div className="inline-flex items-center space-x-2 sm:space-x-3 md:items-end">
                        <div className="flex flex-col">
                            <div className="text-theme-secondary-700 dark:text-theme-dark-200 mb-2 text-sm font-semibold sm:hidden">
                                {t("pages.exchanges.chart.current_price")}
                            </div>

                            <span className="text-theme-secondary-900 dark:text-theme-dark-50 text-lg leading-5.25 font-semibold md:text-2xl md:leading-[29px]!">
                                {mainValueFormatted}
                            </span>
                        </div>

                        <span
                            className={classNames(
                                "hidden items-center rounded px-1 py-px text-xs leading-3.75 font-semibold sm:inline-flex md:mb-[3px]",
                                chart.mainValueVariation === "green" &&
                                    "bg-theme-success-100 text-theme-success-600 dark:border-theme-success-700 dark:text-theme-success-500 border border-transparent dark:bg-transparent",
                                chart.mainValueVariation === "red" &&
                                    "bg-theme-danger-100 text-theme-danger-400 dark:border-theme-danger-400 dark:text-theme-danger-300 border border-transparent dark:bg-transparent",
                            )}
                        >
                            <span>{chart.mainValuePercentage >= 0 ? "+" : ""}</span>
                            <Percentage>{chart.mainValuePercentage}</Percentage>
                        </span>
                    </div>

                    <div className="flex flex-1 sm:block sm:flex-none">
                        <Link href={route("statistics")} className="button-secondary w-full px-4! py-1.5!">
                            <div className="flex items-center justify-center space-x-2">
                                <span className="leading-5">{t("actions.statistics")}</span>

                                <ChevronRightSmallIcon className="h-3 w-3" />
                            </div>
                        </Link>
                    </div>
                </div>

                <div className="border-theme-secondary-300 dark:border-theme-dark-800 mt-4 p-px sm:border-t sm:pt-4 md:mt-6 md:pt-6 lg:w-full">
                    <div className="sm:flex sm:items-end sm:justify-between lg:items-center">
                        <div className="w-full sm:flex sm:pt-0 lg:mt-0 lg:flex-1">
                            <div className="mt-3 hidden sm:mt-0 sm:block">
                                <h3 className="text-theme-secondary-700 dark:text-theme-dark-200 mb-0 text-sm leading-none font-semibold">
                                    {t("pages.exchanges.chart.market-cap")}
                                </h3>

                                <p className="mt-2 text-base leading-5 font-semibold">
                                    {marketCapFormatted ? (
                                        <span className="text-theme-secondary-900 dark:text-theme-dark-50 leading-5">
                                            {marketCapFormatted}
                                            {currencySuffix && <span className="ml-1">{currencySuffix}</span>}
                                        </span>
                                    ) : (
                                        <span className="text-theme-secondary-500 dark:text-theme-dark-700 leading-5">
                                            {t("general.na")}
                                        </span>
                                    )}
                                </p>
                            </div>

                            <div className="dark:border-theme-dark-800 sm:border-theme-secondary-300 mt-4 hidden sm:mt-0 sm:ml-6 sm:block sm:border-l sm:pl-6">
                                <h3 className="text-theme-secondary-700 dark:text-theme-dark-200 mb-0 text-sm leading-none font-semibold">
                                    {t("pages.exchanges.chart.min-price")}
                                </h3>

                                <p className="text-theme-secondary-900 dark:text-theme-dark-50 mt-2 text-base leading-5 font-semibold">
                                    {minPriceFormatted}
                                </p>
                            </div>

                            <div className="dark:border-theme-dark-800 sm:border-theme-secondary-300 mt-4 hidden sm:mt-0 sm:ml-6 sm:block sm:border-l sm:pl-6">
                                <h3 className="text-theme-secondary-700 dark:text-theme-dark-200 mb-0 text-sm leading-none font-semibold">
                                    {t("pages.exchanges.chart.max-price")}
                                </h3>

                                <p className="text-theme-secondary-900 dark:text-theme-dark-50 mt-2 text-base leading-5 font-semibold">
                                    {maxPriceFormatted}
                                </p>
                            </div>
                        </div>

                        <div className="hidden sm:block">
                            <div className="lg:hidden">
                                <PeriodSelect
                                    options={chart.options}
                                    selected={selectedPeriod}
                                    onSelect={updatePeriod}
                                />
                            </div>

                            <div className="hidden lg:inline-flex">
                                <PeriodTabs options={chart.options} selected={selectedPeriod} onSelect={updatePeriod} />
                            </div>
                        </div>
                    </div>

                    <div className="bg-theme-secondary-100 dark:border-theme-dark-800 dark:bg-theme-dark-950 -mx-4 mt-6 -mb-4 hidden p-3 sm:block md:-mx-6 md:-mb-6 md:rounded-b-xl">
                        <ChartCanvas
                            id="exchanges-chart"
                            className="aspect-[4/1] w-full"
                            canvasClassName="max-w-full"
                            datasets={chart.datasets}
                            labels={chart.labels}
                            theme={chartTheme}
                            currency={selectedCurrency}
                            grid
                            tooltips
                            showCrosshair
                            hasDateTimeLabels
                            yPadding={10}
                            xPadding={0}
                            dateUnitOverride={chart.dateUnitOverride ?? null}
                        />
                    </div>
                </div>
            </Card>
        </div>
    );
}
