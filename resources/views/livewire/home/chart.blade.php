<div
    class="flex-col h-full"
    wire:poll.{{ $refreshInterval }}s
>
    <div @class([
        'flex-row justify-between h-full sm:flex-col',
        'hidden md-lg:flex' => ! Network::canBeExchanged(),
        'flex' => Network::canBeExchanged(),
    ])>
        <div class="flex items-center whitespace-nowrap sm:flex-1 sm:justify-between">
            <livewire:home.price-ticker />

            <div class="hidden items-center space-x-3 sm:flex">
                <x-general.dropdown.dropdown
                    dropdown-class="w-50"
                    active-button-class="bg-white text-theme-secondary-700 dark:text-theme-secondary-50 dark:bg-theme-secondary-900 dark:hover:bg-theme-secondary-800 hover:text-theme-secondary-700 hover:bg-theme-secondary-200"
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

                <x-home.exchanges-button />
            </div>
        </div>

        <div class="flex flex-1 justify-end min-w-0 sm:justify-between sm:items-center">
            <div class="hidden w-full sm:flex sm:mt-4 h-[140px] md:h-[116px] lg:mt-[1.125rem]">
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

            <x-home.exchanges-button class="hidden xs:flex items-center sm:hidden" />
        </div>
    </div>

    <x-home.exchanges-button
        class="flex items-center xs:hidden mt-3"
        button-class="flex-1"
    />
</div>

@push('scripts')
    <script src="{{ mix('js/chart-tooltip.js')}}"></script>
@endpush
