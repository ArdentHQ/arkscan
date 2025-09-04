@props([
    'mobile' => false,
])

<x-general.dropdown.filter
    :mobile="$mobile"
    without-text
>
    <div>
        <x-tables.filters.includes.checkbox
            name="select-all"
            model="selectAllFilters.validators"
            :label="trans('tables.filters.validators.select_all')"
        />

        <x-tables.filters.includes.group>
            <x-tables.filters.includes.checkbox
                name="active"
                model="filters.validators.active"
                :label="trans('tables.filters.validators.active')"
            />

            <x-tables.filters.includes.checkbox
                name="standby"
                model="filters.validators.standby"
                :label="trans('tables.filters.validators.standby')"
            />

            <x-tables.filters.includes.checkbox
                name="dormant"
                :label="trans('tables.filters.validators.dormant')"
            />

            <x-tables.filters.includes.checkbox
                name="resigned"
                model="filters.validators.resigned"
                :label="trans('tables.filters.validators.resigned')"
            />
        </x-tables.filters.includes.group>
    </div>
</x-general.dropdown.filter>
