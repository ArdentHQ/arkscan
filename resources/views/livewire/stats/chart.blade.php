@if($show)
    <div
        class="mt-2 space-y-2 md:mt-6"
        wire:poll.{{ $refreshInterval }}s
    >
        <x-general.card class="flex flex-col">
            <div class="flex flex-col space-y-3 sm:flex-row sm:justify-between sm:space-y-0">
                <div class="inline-flex items-center space-x-2 sm:space-x-3 md:items-end">
                    <div class="flex flex-col">
                        <div class="mb-2 text-sm font-semibold sm:hidden leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                            @lang('pages.statistics.chart.current_price')
                        </div>

                        <span class="text-lg font-bold leading-5.25 md:!leading-[29px] md:text-2xl text-theme-secondary-900 dark:text-theme-secondary-200">
                            {{ $mainValueFiat }}
                        </span>
                    </div>

                    <span @class([
                        'hidden sm:inline-flex px-1 py-px items-center text-xs font-semibold rounded leading-3.75 md:mb-[3px]',
                        'border border-transparent bg-theme-success-100 dark:bg-transparent dark:border-theme-success-700 text-theme-success-600 dark:text-theme-success-500' => $mainValueVariation === 'up',
                        'border border-transparent bg-theme-danger-100 dark:bg-transparent dark:border-theme-danger-400 text-theme-danger-400 dark:text-theme-danger-300' => $mainValueVariation === 'down',
                    ])>
                        <span>
                            @if ($mainValuePercentage >= 0)
                                +
                            @endif
                        </span>

                        <x-percentage>{{ $mainValuePercentage }}</x-percentage>
                    </span>
                </div>

                <div class="flex flex-1 sm:block sm:flex-none">
                    <a
                        href="{{ route('exchanges') }}"
                        class="py-1.5 px-4 w-full button-secondary"
                    >
                        <div class="flex justify-center items-center space-x-2">
                            <span class="leading-5">@lang('pages.statistics.exchanges')</span>

                            <x-ark-icon
                                name="arrows.chevron-right-small"
                                size="xs"
                            />
                        </div>
                    </a>
                </div>
            </div>

            <div class="mt-4 sm:pt-4 sm:border-t md:pt-6 md:mt-6 lg:w-full border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="sm:flex sm:justify-between sm:items-end lg:items-center">
                    <div class="w-full sm:flex sm:pt-0 lg:flex-1 lg:mt-0">
                        <div class="hidden mt-3 sm:block sm:mt-0">
                            <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-700 dark:text-theme-dark-200">
                                @lang('pages.statistics.chart.market-cap')
                            </h3>

                            <p class="mt-2 text-base font-semibold leading-5">
                                @if ($marketCapValue)
                                    <span class="leading-5 text-theme-secondary-900 dark:text-theme-dark-50">
                                        {{ $marketCapValue }}

                                        @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                                            {{ Settings::currency() }}
                                        @endif
                                    </span>
                                @else
                                    <span class="leading-5 text-theme-secondary-500 dark:text-theme-secondary-700">
                                        @lang('general.na')
                                    </span>
                                @endif
                            </p>
                        </div>

                        <div class="hidden mt-4 sm:block sm:pl-6 sm:mt-0 sm:ml-6 sm:border-l sm:border-theme-secondary-300 dark:border-theme-secondary-800">
                            <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-700 dark:text-theme-dark-200">
                                @lang('pages.statistics.chart.min-price')
                            </h3>

                            <p class="mt-2 text-base font-semibold leading-5 text-theme-secondary-900 dark:text-theme-dark-50">
                                {{ $minPriceValue }}
                            </p>
                        </div>

                        <div class="hidden mt-4 sm:block sm:pl-6 sm:mt-0 sm:ml-6 sm:border-l sm:border-theme-secondary-300 dark:border-theme-secondary-800">
                            <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-700 dark:text-theme-dark-200">
                                @lang('pages.statistics.chart.max-price')
                            </h3>

                            <p class="mt-2 text-base font-semibold leading-5 text-theme-secondary-900 dark:text-theme-dark-50">
                                {{ $maxPriceValue }}
                            </p>
                        </div>
                    </div>

                    <x-stats.periods-selector
                        wire:model="period"
                        :selected="$period"
                        :options="$options"
                        class="hidden sm:block"
                    />
                </div>

                <div class="p-4 -mx-4 -mb-4 rounded-b sm:hidden md:-mx-6 md:-mb-6 md:rounded-b-xl bg-theme-secondary-100 dark:bg-theme-dark-950">
                    <x-stats.periods-selector
                        wire:model="period"
                        :selected="$period"
                        :options="$options"
                        class="sm:hidden"
                    />

                    <x-ark-chart
                        class="w-full h-[240px]"
                        canvas-class="max-w-full"
                        id="stats-chart-mobile"
                        :data="$datasets->toJson()"
                        labels="[{{ $labels->map(fn ($l) => 'dayjs('.$l.' * 1000).toDate()')->join(',') }}]"
                        :theme="$chartTheme"
                        height="240"
                        :width="null"
                        tooltip-handler="chartTooltip"
                        has-date-time-labels
                        tooltips
                        grid
                        :currency="Settings::currency()"
                        :y-padding="10"
                        :x-padding="0"
                        show-crosshair
                        :date-unit-override="$this->dateUnit"
                    />
                </div>

                <div class="hidden p-3 -mx-4 mt-6 -mb-4 rounded-b sm:block md:-mx-6 md:-mb-6 md:rounded-b-xl bg-theme-secondary-100 dark:bg-theme-dark-950">
                    <x-ark-chart
                        class="w-full h-auto"
                        canvas-class="max-w-full"
                        id="stats-chart"
                        :data="$datasets->toJson()"
                        labels="[{{ $labels->map(fn ($l) => 'dayjs('.$l.' * 1000).toDate()')->join(',') }}]"
                        :theme="$chartTheme"
                        height="288"
                        :width="null"
                        tooltip-handler="chartTooltip"
                        has-date-time-labels
                        tooltips
                        grid
                        :currency="Settings::currency()"
                        :y-padding="10"
                        :x-padding="0"
                        show-crosshair
                        :date-unit-override="$this->dateUnit"
                    />
                </div>
            </div>
        </x-general.card>

        <x-general.card class="p-4 space-y-4 divide-y sm:hidden divide-theme-secondary-300 dark:divide-theme-dark-700">
            <div class="space-y-2">
                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.statistics.chart.market-cap')
                </div>

                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $marketCapValue }}
                </div>
            </div>

            <div class="pt-4 space-y-2">
                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.statistics.chart.min-price')
                </div>

                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $minPriceValue }}
                </div>
            </div>

            <div class="pt-4 space-y-2">
                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.statistics.chart.max-price')
                </div>

                <div class="text-sm font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $maxPriceValue }}
                </div>
            </div>
        </x-general.card>
    </div>
@endif

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            Livewire.hook('message.processed', () => {
                let pos = sessionStorage.getItem('scrollPos');
                setTimeout(() => window.scrollTo(0, parseInt(pos)), 50);
            });
        });

        window.addEventListener('scroll', function () {
            sessionStorage.setItem('scrollPos', window.scrollY.toString());
        });
    </script>
    <script src="{{ mix('js/chart-tooltip.js')}}"></script>
@endpush
