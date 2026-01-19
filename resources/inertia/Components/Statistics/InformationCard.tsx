import { useMemo, useState } from "react";
import Select from "@/Components/General/Select";
import ChartCanvas from "@/Components/Home/Chart/ChartCanvas";
import { InformationCardData, StatisticsChartData, StatisticsPeriod } from "@/Pages/Statistics.contracts";
import { useTranslation } from "react-i18next";
import useSettings from "@/Providers/Settings/useSettings";
import Tooltip from "@/Components/General/Tooltip";

function PeriodSelect({
    options,
    selected,
    onSelect,
}: {
    options: Array<{ value: StatisticsPeriod; label: string }>;
    selected: StatisticsPeriod;
    onSelect: (value: StatisticsPeriod) => void;
}) {
    return (
        <div>
            <Select value={selected} onValueChange={(value) => onSelect(value as StatisticsPeriod)}>
                <Select.Trigger
                    className="form-input transition-default h-10 w-full bg-white !px-3 !py-2 text-left !text-sm font-semibold dark:border-theme-dark-700 dark:bg-theme-dark-900 dark:text-theme-dark-200"
                    placeholder={options.find((option) => option.value === selected)?.label}
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

function Chart({ id, chart }: { id: string; chart: StatisticsChartData }) {
    const { currency: selectedCurrency, theme } = useSettings();

    const chartTheme = useMemo(() => ({ ...chart.theme, mode: theme }), [chart.theme, theme]);

    return (
        <div className="w-full max-w-xs md:w-[132px]">
            <ChartCanvas
                id={id}
                className="h-11 w-full"
                canvasClassName="max-w-full"
                datasets={chart.datasets}
                labels={chart.labels}
                theme={chartTheme}
                currency={selectedCurrency}
                height={50}
                grid={false}
                tooltips={false}
                showCrosshair={false}
                hasDateTimeLabels={false}
                yPadding={0}
                xPadding={0}
            />
        </div>
    );
}

export default function InformationCard({
    id,
    mainTitle,
    mainValue,
    secondaryTitle,
    data,
    defaultPeriod,
    periods,
}: {
    id: string;
    mainTitle: string;
    mainValue: string;
    secondaryTitle: string;
    data: InformationCardData;
    defaultPeriod: StatisticsPeriod;
    periods: StatisticsPeriod[];
}) {
    const { t } = useTranslation();
    const [selectedPeriod, setSelectedPeriod] = useState<StatisticsPeriod>(defaultPeriod);

    const options = useMemo(
        () =>
            periods.map((period) => ({
                value: period,
                label: t(`forms.statistics.periods.${period}`),
            })),
        [periods, t],
    );

    const periodData = data.periods[selectedPeriod];

    return (
        <div className="flex w-full flex-col rounded border border-theme-secondary-300 px-4 py-3 dark:border-theme-dark-700 md:items-stretch md:rounded-xl md:px-6 md:py-4 md:pr-6">
            <div className="mb-4 flex flex-1 flex-col space-y-2 md:mb-4 md:pb-4">
                <div className="mb-0 text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                    {mainTitle}
                </div>

                <div className="text-lg font-semibold leading-5.25 text-theme-secondary-900 dark:text-theme-dark-50 md-lg:text-2xl md-lg:!leading-[29px]">
                    {mainValue}
                </div>
            </div>

            <div className="-mx-4 -mb-4 rounded-b bg-theme-secondary-100 p-4 dark:bg-theme-dark-950 md:-mx-6 md:-my-4 md:w-auto md:rounded-b-xl md:rounded-tr-none md:px-6">
                <PeriodSelect options={options} selected={selectedPeriod} onSelect={setSelectedPeriod} />

                <div className="flex items-end justify-between gap-4 md:w-full">
                    <div className="mt-4">
                        <div className="mb-0 text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                            {secondaryTitle}
                        </div>

                        {periodData.tooltip ? (
                            <Tooltip content={periodData.tooltip}>
                                <div className="mt-2 whitespace-nowrap text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-base md:leading-5">
                                    {periodData.value}
                                </div>
                            </Tooltip>
                        ) : (
                            <div className="mt-2 whitespace-nowrap text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50 md:text-base md:leading-5">
                                {periodData.value}
                            </div>
                        )}
                    </div>

                    <Chart id={`stats-${id}`} chart={periodData.chart} />
                </div>
            </div>
        </div>
    );
}
