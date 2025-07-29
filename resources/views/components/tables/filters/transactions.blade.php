@props([
    'mobile' => false,
])

<x-general.dropdown.filter :mobile="$mobile">
    <div>
        <x-tables.filters.includes.checkbox
            name="select-all"
            :label="trans('tables.filters.transactions.select_all')"
            class="pb-4 mb-4 border-b md:pb-2 md:mb-1 border-theme-secondary-300 dark:border-theme-dark-800"
            model="selectAllFilters"
        />

        <x-tables.filters.includes.group :label="trans('tables.filters.transactions.addressing')">
            <x-tables.filters.includes.checkbox name="outgoing">
                <x-slot name="label">
                    <span>@lang('tables.filters.transactions.outgoing')</span>

                    <span class="text-theme-secondary-500 dark:text-theme-dark-500">
                        (@lang('tables.filters.transactions.to'))
                    </span>
                </x-slot>
            </x-tables.filters.includes.checkbox>

            <x-tables.filters.includes.checkbox name="incoming">
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
