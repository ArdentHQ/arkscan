@props([
    'mobile' => false,
])

<x-general.dropdown.filter :mobile="$mobile">
    <div>
        <x-tables.filters.includes.checkbox
            name="select-all"
            :label="trans('tables.filters.transactions.select_all')"
            model="selectAllFilters.transactions"
        />

        <x-tables.filters.includes.group :label="trans('tables.filters.transactions.addressing')">
            <x-tables.filters.includes.checkbox
                name="outgoing"
                model="filters.transactions.outgoing"
            >
                <x-slot name="label">
                    <span>@lang('tables.filters.transactions.outgoing')</span>

                    <span class="text-theme-secondary-500 dark:text-theme-dark-500">
                        (@lang('tables.filters.transactions.to'))
                    </span>
                </x-slot>
            </x-tables.filters.includes.checkbox>

            <x-tables.filters.includes.checkbox
                name="incoming"
                model="filters.transactions.incoming"
            >
                <x-slot name="label">
                    <span>@lang('tables.filters.transactions.incoming')</span>

                    <span class="text-theme-secondary-500 dark:text-theme-dark-500">
                        (@lang('tables.filters.transactions.from'))
                    </span>
                </x-slot>
            </x-tables.filters.includes.checkbox>
        </x-tables.filters.includes.group>

        <x-tables.filters.includes.group :label="trans('tables.filters.transactions.types')">
            <x-tables.filters.includes.checkbox
                name="transfers"
                model="filters.transactions.transfers"
                :label="trans('tables.filters.transactions.transfers')"
            />

            <x-tables.filters.includes.checkbox
                name="multipayments"
                model="filters.transactions.multipayments"
                :label="trans('tables.filters.transactions.multipayments')"
            />

            <x-tables.filters.includes.checkbox
                name="votes"
                model="filters.transactions.votes"
                :label="trans('tables.filters.transactions.votes')"
            />

            <x-tables.filters.includes.checkbox
                name="validator"
                model="filters.transactions.validator"
                :label="trans('tables.filters.transactions.validator')"
            />

            <x-tables.filters.includes.checkbox
                name="username"
                model="filters.transactions.username"
                :label="trans('tables.filters.transactions.username')"
            />

            <x-tables.filters.includes.checkbox
                name="contract_deployment"
                model="filters.transactions.contract_deployment"
                :label="trans('tables.filters.transactions.contract_deployment')"
            />

            <x-tables.filters.includes.checkbox
                name="others"
                model="filters.transactions.others"
                :label="trans('tables.filters.transactions.others')"
            />
        </x-tables.filters.includes.group>
    </div>
</x-general.dropdown.filter>
