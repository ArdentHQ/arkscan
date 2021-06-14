@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.9.1/dayjs.min.js"></script>
<script>
    window.makeChart = (identifier, coloursScheme) => {
        return {
            period: "Day",
            identifier,
            coloursScheme,
            chart: null,
            data: {
                marketHistoricalDay: @json($data['day']),
                marketHistoricalWeek: @json($data['week']),
                marketHistoricalMonth: @json($data['month']),
                marketHistoricalQuarter: @json($data['quarter']),
                marketHistoricalYear: @json($data['year']),
            },
            currency: "{{ Network::currency() }}",
            dateAt: "",
            priceAt: null,
            priceMin: null,
            priceMax: null,
            priceAvg: null,
            dropdownOpen: false,
            localizedPeriod: null,
            isDarkTheme: "{{ Settings::darkTheme() }}",
            isVisible: "{{ $isVisible }}",
            getMarketAverage(period) {
                const market = this.data[`marketHistorical${period}`]

                this.priceMin = market.min;
                this.priceAvg = market.avg;
                this.priceMax = market.max;

                return market;
            },
            toggleChart() {
                this.isVisible = ! this.isVisible;
            },
            getThemeColors() {
                let themeColours = {
                    light: {
                        gridLines: "#DBDEE5",
                        ticks: "#B0B0B8",
                        pointBackgroundColor: "#FFFFFF",
                    },
                    dark: {
                        gridLines: "#3C4249",
                        ticks: "#7E8A9C",
                        pointBackgroundColor: "#212225",
                    }
                };

                return this.isDarkTheme ? themeColours.dark : themeColours.light;
            },
            toggleDarkMode() {
                this.isDarkTheme = ! this.isDarkTheme;
                this.updateChartColors();
            },
            updateChartColors() {
                const themeColours = this.getThemeColors();

                this.chart.data.datasets.forEach((dataset) => {
                    dataset.pointBackgroundColor = themeColours.pointBackgroundColor;
                });

                this.chart.options.scales.yAxes.forEach((yAxe) => {
                    yAxe.gridLines.color = themeColours.gridLines;
                    yAxe.ticks.fontColor = themeColours.ticks
                })
                this.chart.options.scales.xAxes.forEach((xAxe) => {
                    xAxe.gridLines.color = themeColours.gridLines;
                    xAxe.ticks.fontColor = themeColours.ticks
                })

                this.chart.options.tooltips.titleFontColor = themeColours.ticks;

                this.chart.update();
            },
            renderChart() {
                const themeColours = this.getThemeColors();

                const fontConfig = {
                    fontColor: themeColours.ticks,
                    fontSize: 14,
                    fontStyle: 600,
                };

                const scaleCorrection = 1000;

                let ctx = document.getElementById(this.identifier).getContext("2d");

                this.chart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: this.getMarketAverage('Day').labels,
                        datasets: [
                            {
                                borderColor: this.coloursScheme,
                                pointRadius: 4,
                                pointHoverRadius: 12,
                                pointHoverBorderWidth: 3,
                                pointHoverBackgroundColor: "rgba(204, 230, 211, 0.5)",
                                pointHitRadius: 12,
                                pointBackgroundColor: themeColours.pointBackgroundColor,
                                borderWidth: 3,
                                type: "line",
                                fill: false,
                                data: this.getMarketAverage('Day').datasets,
                                hidden: false,
                            },
                        ],
                    },
                    // Configuration options go here
                    options: {
                        showScale: true,
                        responsive: true,
                        maintainAspectRatio: false,
                        elements: {
                            line: {
                                cubicInterpolationMode: "monotone",
                            },
                        },
                        legend: {
                            display: false,
                        },
                        layout: {
                            padding: {
                                left: 0,
                                right: 0,
                                top: 10,
                                bottom: 10,
                            },
                        },
                        scales: {
                            yAxes: [
                                {
                                    type: "linear",
                                    position: "left",
                                    stacked: true,
                                    gridLines: {
                                        color: themeColours.gridLines,
                                        display: true,
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        padding: 15,
                                        ...fontConfig,
                                        callback: (value, index, values) => {
                                            // TODO: Proper implementation
                                            if (index % 2 === 0) {
                                                return;
                                            }

                                            const formatConfig = {
                                                currency: this.currency,
                                            };

                                            const price = value / scaleCorrection;

                                            if (price < 1e-4) {
                                                formatConfig.maximumFractionDigits = 8;
                                            } else if (price < 1e-2) {
                                                formatConfig.maximumFractionDigits = 5;
                                            } else {
                                                formatConfig.maximumFractionDigits = 3;
                                            }

                                            return `{{ Network::currencySymbol() }}${value}`;
                                        },
                                    },
                                },
                            ],
                            xAxes: [
                                {
                                    gridLines: {
                                        color: themeColours.gridLines,
                                        drawBorder: false,
                                        display: true,
                                    },
                                    ticks: {
                                        padding: 10,
                                        ...fontConfig,
                                        callback: (value, index, values) => {
                                            if (
                                                this.period !== "Day" &&
                                                index === values.length - 1
                                            ) {
                                                return "Today";
                                            } else if (this.period === "Week") {
                                                const width = this.$el.clientWidth;

                                                if (width > 1200) {
                                                    // TODO: DW returns a day of the week, where value would be "Friday" for example
                                                    return value;
                                                } else {
                                                    // TODO: DW returns the abbreviation of a day of the week, where value would be "FRI" for example
                                                    return value;
                                                }
                                            } else if (this.period === "Month") {
                                                return value;
                                            }

                                            return value;
                                        },
                                    },
                                },
                            ],
                        },
                        tooltips: {
                            displayColors: false,
                            mode: "interpolate",
                            intersect: false,
                            mode: "index",
                            axis: "x",
                            titleFontColor: themeColours.ticks,
                            callbacks: {
                                label: (item) => {
                                    // TODO: Rounded circle on the left of the label
                                    return `${item.yLabel.toFixed(2)} ${this.currency}`;
                                },
                                title: (items, data) => {},
                            },
                        },
                    },
                });

                return this.chart;
            },

            updateLabels() {
                return this.getMarketAverage(this.period).labels;
            },

            updateTicks() {
                return this.getMarketAverage(this.period).datasets;
            },

            setPeriod(period) {
                this.period = period.charAt(0).toUpperCase() + period.slice(1);

                updatedTicks = this.updateTicks();

                this.chart.data.labels = this.updateLabels();
                this.chart.data.datasets[0].data = updatedTicks;

                // Render the chart synchronously and without an animation.
                this.chart.update(0);
            },

            isActivePeriod(period) {
                return period === this.period;
            },
        };
    };
</script>
@endpush

<div
    x-data="makeChart('{{ $identifier }}', '{{ $coloursScheme }}')"
    x-init="renderChart()"
    x-on:toggle-dark-mode.window="toggleDarkMode()"
    x-on:chart-period-selected.window="setPeriod($event.detail)"
    x-on:{{ $alpineShow }}.window="toggleChart()"
    x-show="isVisible"
    class="flex flex-col w-full bg-white dark:border-black border-theme-secondary-100 dark:bg-theme-secondary-900"
>
    <div class="flex flex-col w-full">
        <div class="flex relative justify-between items-center w-full">
            <h3 class="w-full">@lang("pages.home.charts.{$identifier}")</h3>

            <div>
                <x-ark-rich-select
                    wrapper-class="left-0 mt-3"
                    dropdown-class="right-0 mt-1 origin-top-right"
                    initial-value="day"
                    button-class="block font-medium text-left bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200"
                    :options="collect(trans('pages.home.charts.periods'))->keys()->mapWithKeys(function ($period) {
                        return [$period => __('pages.home.charts.periods.' . $period)];
                    })->toArray()"
                    dispatch-event="chart-period-selected"
                />
            </div>
        </div>
        <div class="flex justify-between mt-5 mb-5 w-full">
            <div
                class="flex items-center pr-5 mr-5 border-r border-theme-secondary-200 dark:border-theme-secondary-800">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">@lang("pages.home.charts.min_{$identifier}")</span>
                    <span class="font-semibold dark:text-theme-secondary-200" x-text="priceMin + ` ${currency}`"></span>
                </div>
            </div>

            <div
                class="flex items-center pr-5 mr-5 border-r border-theme-secondary-200 dark:border-theme-secondary-800">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">@lang("pages.home.charts.max_{$identifier}")</span>
                    <span class="font-semibold dark:text-theme-secondary-200" x-text="priceMax + ` ${currency}`"></span>
                </div>
            </div>

            <div class="flex items-center pr-5 mr-5">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">@lang("pages.home.charts.avg_{$identifier}")</span>
                    <span class="font-semibold dark:text-theme-secondary-200" x-text="priceAvg + ` ${currency}`"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex w-full" style="height: 340px;">
        <canvas id="{{ $identifier ?? 'priceChart' }}"></canvas>
    </div>
</div>
