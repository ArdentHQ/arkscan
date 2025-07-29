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
            :label="trans('tables.filters.validators.select_all')"
            class="pb-4 mb-4 border-b md:pb-2 md:mb-1 border-theme-secondary-300 dark:border-theme-dark-800"
            model="selectAllFilters"
        />

        <x-tables.filters.includes.group>
            <x-tables.filters.includes.checkbox
                name="active"
                :label="trans('tables.filters.validators.active')"
            />

            <x-tables.filters.includes.checkbox
                name="standby"
                :label="trans('tables.filters.validators.standby')"
            />

            <x-tables.filters.includes.checkbox
                name="dormant"
                :label="trans('tables.filters.validators.dormant')"
            />

            <x-tables.filters.includes.checkbox
                name="resigned"
                :label="trans('tables.filters.validators.resigned')"
            />
        </x-tables.filters.includes.group>
    </div>
</x-general.dropdown.filter>
