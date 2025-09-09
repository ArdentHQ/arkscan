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
            :label="trans('tables.filters.transactions.select_all')"
            model="selectAllFilters.default"
        />

        <x-tables.filters.includes.group>
            <x-tables.filters.includes.checkbox
                name="transfers"
                :label="trans('tables.filters.transactions.transfers')"
            />

            <x-tables.filters.includes.checkbox
                name="multipayments"
                :label="trans('tables.filters.transactions.multipayments')"
            />

            <x-tables.filters.includes.checkbox
                name="votes"
                :label="trans('tables.filters.transactions.votes')"
            />

            <x-tables.filters.includes.checkbox
                name="validator"
                :label="trans('tables.filters.transactions.validator')"
            />

            <x-tables.filters.includes.checkbox
                name="username"
                :label="trans('tables.filters.transactions.username')"
            />

            <x-tables.filters.includes.checkbox
                name="contract_deployment"
                :label="trans('tables.filters.transactions.contract_deployment')"
            />

            <x-tables.filters.includes.checkbox
                name="others"
                :label="trans('tables.filters.transactions.others')"
            />
        </x-tables.filters.includes.group>
    </div>
</x-general.dropdown.filter>
