@props([
    'items',
    'label',
])

<div>
    <x-input.js-select
        id="dateRange"
        :label="$label"
        dropdown-width="w-full sm:w-100"
        :items="$items"
        :extra-items="[
            [
                'value' => 'custom',
                'text'  => trans('general.custom')
            ]
        ]"
    />

    <div
        x-data="{
            dateToPicker: null,
            dateFromPicker: null,

            setDateToInstance(instance) {
                this.dateToPicker = instance;
            },
            setDateFromInstance(instance) {
                this.dateFromPicker = instance;
            },

            setDateTo(date) {
                this.dateToPicker.setMinDate(date);
            },
            setDateFrom(date) {
                this.dateFromPicker.setMaxDate(date);
            },
        }"
        x-show="dateRange === 'custom'"
        class="flex py-4 px-6 -mx-6 mt-4 space-x-3 bg-theme-primary-50 dark:bg-theme-dark-950"
    >
        <x-input.date-picker
            name="exportDateFrom"
            :label="trans('general.export.date_from')"
            min-date="new Date({{ Network::epoch()->timestamp }}000)"
            locale=""
            class="flex-1"
            x-model="dateFrom"
            x-on-change="setDateTo"
            x-init="setDateFromInstance"
        />

        <x-input.date-picker
            name="exportDateTo"
            :label="trans('general.export.date_to')"
            min-date="new Date({{ Network::epoch()->timestamp }}000)"
            locale=""
            class="flex-1"
            x-model="dateTo"
            x-on-change="setDateFrom"
            x-init="setDateToInstance"
        />
    </div>
</div>
