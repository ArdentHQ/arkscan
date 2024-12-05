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
            class="pb-4 mb-4 border-b md:pb-2 md:mb-1 border-theme-secondary-300 dark:border-theme-dark-800"
            model="selectAllFilters"
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
                name="unvotes"
                :label="trans('tables.filters.transactions.unvotes')"
            />

            <x-tables.filters.includes.checkbox
                name="validator_registration"
                :label="trans('tables.filters.transactions.validator_registration')"
            />

            <x-tables.filters.includes.checkbox
                name="validator_resignation"
                :label="trans('tables.filters.transactions.validator_resignation')"
            />

            <x-tables.filters.includes.checkbox
                name="username_registration"
                :label="trans('tables.filters.transactions.username_registration')"
            />

            <x-tables.filters.includes.checkbox
                name="username_resignation"
                :label="trans('tables.filters.transactions.username_resignation')"
            />

            <x-tables.filters.includes.checkbox
                name="others"
                :label="trans('tables.filters.transactions.others')"
            />
        </x-tables.filters.includes.group>
    </div>
</x-general.dropdown.filter>
