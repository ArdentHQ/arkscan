@props([
    'items',
    'label',
])

<div>
    <x-input.js-select
        id="dateRange"
        :label="$label"
        dropdown-width="w-full sm:w-[400px]"
        :items="$items"
        :extra-items="[
            [
                'value' => 'custom',
                'text'  => trans('general.custom')
            ]
        ]"
    />

    <div
        x-show="dateRange === 'custom'"
        class="flex space-x-3 -mx-6 px-6 bg-theme-primary-50 dark:bg-theme-dark-950 py-4 mt-4"
    >
        <x-input.date-picker
            name="exportDateFrom"
            :label="trans('general.export.date_from')"
            min-date="new Date({{ Network::epoch()->timestamp }}000)"
            locale=""
            class="flex-1"
            x-model="dateFrom"
        />

        <x-input.date-picker
            name="exportDateTo"
            :label="trans('general.export.date_to')"
            min-date="new Date({{ Network::epoch()->timestamp }}000)"
            locale=""
            class="flex-1"
            x-model="dateTo"
        />
    </div>
</div>
