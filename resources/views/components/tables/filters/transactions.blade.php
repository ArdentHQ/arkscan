<x-general.dropdown.dropdown
    placement="right-start"
    :placement-fallbacks="['bottom', 'bottom-end', 'left-start']"
    dropdown-class="w-full px-6 md:w-[284px] table-filter md:px-8"
    :close-on-click="false"
    class="flex-1"
    dropdown-wrapper-class="w-full"
>
    <x-slot name="button" class="w-full rounded">
        <div class="md:p-2 button-secondary flex items-center sm:py-1.5 sm:px-4 flex-1 sm:flex-none justify-center">
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="md:hidden ml-2">
                @lang('actions.filter')
            </div>
        </div>
    </x-slot>

    <div>
        <x-tables.filters.includes.checkbox
            name="select-all"
            :label="trans('tables.filters.transactions.select_all')"
            class="border-b border-theme-secondary-300 dark:border-theme-secondary-700 mb-1"
        />

        <x-tables.filters.includes.group-label :text="trans('tables.filters.transactions.addressing')" />

        <x-tables.filters.includes.checkbox name="outgoing">
            <x-slot name="label">
                <span>@lang('tables.filters.transactions.outgoing')</span>

                <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                    (@lang('tables.filters.transactions.to'))
                </span>
            </x-slot>
        </x-tables.filters.includes.checkbox>

        <x-tables.filters.includes.checkbox name="incoming">
            <x-slot name="label">
                <span>@lang('tables.filters.transactions.incoming')</span>

                <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                    (@lang('tables.filters.transactions.from'))
                </span>
            </x-slot>
        </x-tables.filters.includes.checkbox>

        <x-tables.filters.includes.group-label :text="trans('tables.filters.transactions.types')" />

        <x-tables.filters.includes.checkbox
            name="transfers"
            :label="trans('tables.filters.transactions.transfers')"
        />

        <x-tables.filters.includes.checkbox
            name="votes"
            :label="trans('tables.filters.transactions.votes')"
        />

        <x-tables.filters.includes.checkbox
            name="multipayments"
            :label="trans('tables.filters.transactions.multipayments')"
        />

        <x-tables.filters.includes.checkbox
            name="others"
            :label="trans('tables.filters.transactions.others')"
        />
    </div>
</x-general.dropdown.dropdown>
