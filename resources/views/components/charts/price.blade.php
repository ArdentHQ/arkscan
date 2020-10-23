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

                getMarketAverage(period) {
                    const market = this.data[`marketHistorical${period}`]

                    this.priceMin = market.min;
                    this.priceAvg = market.avg;
                    this.priceMax = market.max;

                    return market;
                },
                renderChart() {
                    let themeColours = {
                        light: {
                            gridLines: "#DBDEE5",
                            ticks: "#B0B0B8",
                        },
                        dark: {
                            gridLines: "#3C4249",
                            ticks: "#7E8A9C",
                        }
                    };

                    themeColours = this.isDarkTheme ? themeColours.dark : themeColours.light;

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
                                    pointBackgroundColor: "#FFFFFF",
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
                                    tension: 0,
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

<div x-data="makeChart('{{ $identifier }}', '{{ $coloursScheme }}')" x-init="renderChart()"
    class="flex flex-col w-full bg-white border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex flex-col w-full">
        <div class="relative flex items-center justify-between w-full">
            <h2 class="text-2xl">@lang("pages.home.charts.{$identifier}")</h2>

            <x-ark-dropdown dropdown-classes="left-0 w-32 mt-3" button-class="w-32 h-10 dropdown-button"
                :init-alpine="false">
                @slot('button')
                <div
                    class="flex items-center justify-end w-full space-x-2 font-semibold flex-inline text-theme-secondary-700">
                    <span x-text="period"></span>
                    <span :class="{ 'rotate-180': open }" class="transition duration-150 ease-in-out">
                        @svg('chevron-up', 'h-3 w-3')
                    </span>
                </div>
                @endslot
                <div class="py-3">
                    @foreach (array_keys(trans('pages.home.charts.periods')) as $period)
                    <div class="cursor-pointer dropdown-entry"
                        :class="{ 'text-theme-danger-400 bg-theme-danger-100': isActivePeriod('{{ ucfirst($period) }}') === true}"
                        @click="setPeriod('{{ $period }}')">
                        @lang("pages.home.charts.periods." . $period)
                    </div>
                    @endforeach
                </div>
            </x-ark-dropdown>
        </div>
        <div class="flex justify-between w-full mt-5 mb-5">
            <div
                class="flex items-center pr-5 mr-5 border-r border-theme-secondary-200 dark:border-theme-secondary-800">
                <div class="flex flex-col">
                    <span
                        class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">@lang("pages.home.charts.min_{$identifier}")</span>
                    <span class="font-semibold dark:text-theme-secondary-200" x-text="priceMin + ` ${currency}`"></span>
                </div>
            </div>

            <div
                class="flex items-center pr-5 mr-5 border-r border-theme-secondary-200 dark:border-theme-secondary-800">
                <div class="flex flex-col">
                    <span
                        class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">@lang("pages.home.charts.max_{$identifier}")</span>
                    <span class="font-semibold dark:text-theme-secondary-200"
                        x-text="priceMax + ` ${currency}`">0.02477504 BTC</span>
                </div>
            </div>

            <div class="flex items-center pr-5 mr-5">
                <div class="flex flex-col">
                    <span
                        class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">@lang("pages.home.charts.avg_{$identifier}")</span>
                    <span class="font-semibold dark:text-theme-secondary-200"
                        x-text="priceAvg + ` ${currency}`">0.01570092 BTC</span>
                </div>
            </div>
        </div>
    </div>

    <div class="flex w-full" style="height: 340px;">
        <canvas id="{{ $identifier ?? 'priceChart' }}"></canvas>
    </div>
</div>