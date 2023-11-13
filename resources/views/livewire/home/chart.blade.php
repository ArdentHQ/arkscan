<div
    class="flex-col h-full"
    wire:poll.{{ $refreshInterval }}s
>
    <div @class([
        'flex-row justify-between space-x-4 h-full md:flex-col md:space-x-0',
        'hidden md-lg:flex' => ! Network::canBeExchanged(),
        'flex' => Network::canBeExchanged(),
    ])>
        <div class="flex items-center whitespace-nowrap md:flex-1 md:justify-between">
            <x-home.stat
                :title="trans('pages.home.statistics.currency_price', ['currency' => Network::currency()])"
                class="md:hidden"
            >
                {{ $mainValueFiat }}

                @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                    {{ Settings::currency() }}
                @endif
            </x-home.stat>

            <p class="hidden items-center space-x-2 sm:space-x-3 md:inline-flex">
                <span class="text-sm font-semibold sm:text-3xl md:text-2xl text-theme-secondary-900 dark:text-theme-secondary-200">
                    <span>
                        {{ $mainValueFiat }}

                        @if (ExplorerNumberFormatter::hasSymbol(Settings::currency()))
                            {{ Settings::currency() }}
                        @endif
                    </span>
                </span>
            </p>

            <div class="hidden items-center space-x-3 md:flex">
                <x-general.dropdown.dropdown
                    dropdown-class="w-50"
                    active-button-class="md:bg-white bg-theme-secondary-200 text-theme-secondary-700 md:dark:text-theme-secondary-50 md:hover:text-theme-secondary-700 md:hover:bg-theme-secondary-200 md:dark:bg-theme-secondary-900 dark:bg-theme-secondary-800 dark:hover:bg-theme-secondary-800 dark:text-theme-secondary-200"
                >
                    <x-slot
                        name="button"
                        class="justify-between py-1.5 px-3 text-sm button-secondary w-[116px] shadow-px shadow-theme-secondary-300"
                    >
                        <span>@lang('pages.home.charts.periods.'.$this->period)</span>

                        <span
                            class="transition-default"
                            :class="{ 'rotate-180': dropdownOpen }"
                        >
                            <x-ark-icon
                                name="arrows.chevron-down-small"
                                size="w-3 h-3"
                            />
                        </span>
                    </x-slot>

                    @foreach ($options as $period => $lang)
                        <x-general.dropdown.list-item
                            :is-active="$period === $this->period"
                            wire:click="setPeriod('{{ $period }}')"
                        >
                            {{ $lang }}
                        </x-general.dropdown.list-item>
                    @endforeach
                </x-general.dropdown.dropdown>

                @if (Network::canBeExchanged())
                    <div>
                        <a
                            href="{{ route('exchanges') }}"
                            class="py-1.5 px-4 button button-secondary"
                        >
                            @lang('actions.exchanges')
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-1 justify-end min-w-0 sm:justify-between sm:items-center sm:pl-9 md:pl-0">
            <div class="flex w-full md:hidden max-h-[39px] max-w-[258px] sm:max-w-[133px]">
                <livewire:price-stats />
            </div>

            <div class="hidden w-full md:flex md:mt-4 h-[112px]">
                <x-ark-chart
                    class="w-full h-auto"
                    canvas-class="max-w-full"
                    id="price-chart"
                    :data="$datasets->toJson()"
                    labels="[{{ $labels->map(fn ($l) => 'dayjs('.$l.' * 1000).toDate()')->join(',') }}]"
                    :theme="$chartTheme"
                    height="109"
                    :width="null"
                    tooltip-handler="chartTooltip"
                    has-date-time-labels
                    tooltips
                    grid
                    :currency="Settings::currency()"
                    :y-padding="10"
                    :x-padding="0"
                    show-crosshair
                />
            </div>

            @if (Network::canBeExchanged())
                <div class="hidden sm:block md:hidden">
                    <a
                        href="{{ route('exchanges') }}"
                        class="py-1.5 px-4 w-full button button-secondary"
                    >
                        @lang('actions.exchanges')
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if (Network::canBeExchanged())
        <div class="mt-3 sm:hidden">
            <a
                href="{{ route('exchanges') }}"
                class="py-1.5 px-4 w-full button button-secondary"
            >
                @lang('actions.exchanges')
            </a>
        </div>
    @endif
</div>

@push('scripts')
    <script src="{{ mix('js/chart-tooltip.js')}}"></script>
@endpush
