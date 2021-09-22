@props([
    'id',
    'mainTitle',
    'mainValue',
    'secondaryTitle',
    'secondaryValue',
    'secondaryTooltip' => null,
    'tertiaryTitle' => null,
    'tertiaryValue' => null,
    'chart' => null,
    'chartTheme' => 'grey',
    'options',
    'model',
    'selected',
])

<x-general.card with-border class="flex flex-col gap-6 w-full">
    <div class="xl:w-full">
        <h2 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-900 dark:text-theme-secondary-200">{{ $mainTitle }}</h2>
        <p class="mt-3 text-lg font-bold sm:text-2xl text-theme-secondary-900 dark:text-theme-secondary-200">{{ $mainValue }}</p>
    </div>

    <div class="pt-6 w-full border-t border-theme-secondary-300 dark:border-theme-secondary-800">
        <div wire:ignore>
            <x-ark-rich-select
                wire:model="{{ $model }}"
                wrapper-class="relative left-0 xl:inline-block"
                dropdown-class="left-0 mt-1 origin-top-left"
                button-class="block text-sm font-semibold text-left bg-transparent text-theme-secondary-700 dark:text-theme-secondary-200"
                :initial-value="$selected"
                :placeholder="$selected"
                :options="$options"
            />
        </div>

        <div class="flex flex-col gap-5 sm:flex-row sm:items-end xl:w-full @if($chart) sm:justify-between @endif">
            <div class="">
                <h3 class="mt-4 mb-0 text-sm font-semibold leading-none text-theme-secondary-500 dark:text-theme-secondary-700">{{ $secondaryTitle }}</h3>
                <p class="mt-2 text-base font-semibold text-theme-secondary-700 dark:text-theme-secondary-200"
                   @if($secondaryTooltip)
                   data-tippy-content="{{ $secondaryTooltip }}"
                   wire:key="{{ md5($secondaryTooltip) }}"
                   @endif
                >
                    {{ $secondaryValue }}
                </p>
            </div>

            @if($chart)
                <div class="w-full max-w-xs xl:w-1/3">
                    <x-ark-chart
                        class="w-full h-auto"
                        id="stats-insight-{{ $id }}"
                        :data="collect($chart->get('datasets'))->toJson()"
                        :labels="collect($chart->get('labels'))->keys()->toJson()"
                        :theme="$chartTheme"
                        width="200"
                        height="50"
                        :currency="Settings::currency()"
                    />
                </div>
            @else
                <div class="sm:pl-6 sm:border-l border-theme-secondary-300 dark:border-theme-secondary-800">
                    <h3 class="mb-0 text-sm font-semibold leading-none text-theme-secondary-500 dark:text-theme-secondary-700">{{ $tertiaryTitle }}</h3>
                    <p class="mt-2 text-base font-semibold text-theme-secondary-700 dark:text-theme-secondary-200">{{ $tertiaryValue }}</p>
                </div>
            @endif
        </div>
    </div>
</x-general.card>
