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

<x-general.card
    class="flex flex-col w-full md:flex-row md:pr-0 xl:flex-col xl:pr-6"
    with-border
>
    <div class="flex flex-col flex-1 space-y-2 mb-4 md:mb-0 xl:mb-6 xl:pb-6">
        <h2 class="mb-0 text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
            {{ $mainTitle }}
        </h2>

        <div class="text-lg font-bold md:text-2xl leading-5.25 md:!leading-[29px] text-theme-secondary-900 dark:text-theme-secondary-200">
            {{ $mainValue }}
        </div>
    </div>

    <div class="-mx-4 -mb-4 p-4 md:p-6 md:mx-0 md:-my-6 xl:-mx-6 xl:-mb-6 md:w-[24.5rem] xl:w-auto bg-theme-secondary-100 dark:bg-theme-dark-950 rounded-b md:rounded-b-none md:rounded-r-xl xl:rounded-tr-none xl:rounded-b-xl">
        <div wire:ignore>
            <x-rich-select
                wire:model="{{ $model }}"
                wrapper-class="relative left-0 w-full xl:inline-block"
                dropdown-class="left-0 mt-1 origin-top-left"
                button-class="inline-block w-full text-left !px-3 !py-2 form-input transition-default dark:bg-theme-secondary-900 dark:border-theme-secondary-800 !text-sm font-semibold leading-4.25"
                :initial-value="$selected"
                :placeholder="$selected"
                :options="$options"
            />
        </div>

        <div @class([
            'flex flex-col gap-5 sm:flex-row sm:items-end xl:w-full',
            'sm:justify-between' => $chart,
        ])>
            <div class="mt-4">
                <h3 class="mb-0 text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                    {{ $secondaryTitle }}
                </h3>

                <div
                    class="mt-2 text-sm font-semibold md:text-base md:leading-5 leading-4.25 text-theme-secondary-900 dark:text-theme-secondary-200"

                    @if($secondaryTooltip)
                        data-tippy-content="{{ $secondaryTooltip }}"
                        wire:key="{{ md5($secondaryTooltip) }}"
                    @endif
                >
                    {{ $secondaryValue }}
                </div>
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
                    <h3 class="mb-0 text-sm font-semibold leading-4.25 text-theme-secondary-700 dark:text-theme-dark-200">
                        {{ $tertiaryTitle }}
                    </h3>

                    <div class="mt-2 text-sm font-semibold md:text-base md:leading-5 leading-4.25 text-theme-secondary-900 dark:text-theme-secondary-200">
                        {{ $tertiaryValue }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-general.card>
