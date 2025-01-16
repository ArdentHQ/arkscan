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

<x-general.card class="flex flex-col py-4 w-full md:flex-col md:items-stretch md:pr-6 md:py-4">
    <div class="flex flex-col flex-1 mb-4 space-y-2 md:pb-4 md:mb-4">
        <div class="mb-0 text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
            {{ $mainTitle }}
        </div>

        <div class="text-lg font-semibold md-lg:text-2xl leading-5.25 md-lg:!leading-[29px] text-theme-secondary-900 dark:text-theme-dark-50">
            {{ $mainValue }}
        </div>
    </div>

    <div class="p-4 -mx-4 -mb-4 rounded-b md:px-6 md:-mx-6 md:-my-4 md:w-auto md:rounded-b-xl md:rounded-tr-none bg-theme-secondary-100 dark:bg-theme-dark-950">
        <div wire:ignore>
            <x-rich-select
                wire:model.live="{{ $model }}"
                wrapper-class="relative left-0 w-full md:inline-block"
                dropdown-class="left-0 mt-1 origin-top-left"
                button-class="inline-block w-full text-left !px-3 !py-2 form-input transition-default dark:bg-theme-dark-900 dark:border-theme-dark-700 !text-sm font-semibold"
                :initial-value="$selected"
                :placeholder="$selected"
                :options="$options"
            />
        </div>

        <div @class([
            'flex gap-4 sm:gap-6 md:w-full',
            'flex-col sm:flex-row sm:items-end' => ! $chart,
            'justify-between items-end' => $chart,
        ])>
            <div class="mt-4">
                <div class="mb-0 text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                    {{ $secondaryTitle }}
                </div>

                <div
                    class="mt-2 text-sm font-semibold whitespace-nowrap md:text-base md:leading-5 text-theme-secondary-900 dark:text-theme-dark-50"

                    @if($secondaryTooltip)
                        data-tippy-content="{{ $secondaryTooltip }}"
                        wire:key="{{ md5($secondaryTooltip) }}"
                    @endif
                >
                    {{ $secondaryValue }}
                </div>
            </div>

            @if($chart)
                <div class="w-full max-w-xs md:w-[132px]">
                    <x-ark-chart
                        class="w-full h-11"
                        canvas-class="max-w-full"
                        id="stats-insight-{{ $id }}"
                        :data="collect($chart->get('datasets'))->toJson()"
                        :labels="collect($chart->get('labels'))->keys()->toJson()"
                        :theme="$chartTheme"
                        height="50"
                        :currency="Settings::currency()"
                    />
                </div>
            @else
                <div class="sm:pl-6 sm:border-l border-theme-secondary-300 dark:border-theme-dark-800">
                    <div class="mb-0 text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200">
                        {{ $tertiaryTitle }}
                    </div>

                    <div class="mt-2 text-sm font-semibold md:text-base md:leading-5 text-theme-secondary-900 dark:text-theme-dark-50">
                        {{ $tertiaryValue }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-general.card>
