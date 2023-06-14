<x-general.dropdown.dropdown
    placement="right-start"
    :placement-fallbacks="['bottom', 'bottom-end', 'left-start']"
    dropdown-class="px-6 w-full md:px-8 table-filter md:w-[284px]"
    :close-on-click="false"
    class="flex-1"
    dropdown-wrapper-class="w-full"
>
    <x-slot name="button" class="w-full rounded">
        <div class="flex flex-1 justify-center items-center sm:flex-none sm:py-1.5 sm:px-4 md:p-2 button-secondary">
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="ml-2 md:hidden">
                @lang('actions.filter')
            </div>
        </div>
    </x-slot>

    <div>
        <x-tables.filters.includes.checkbox
            name="select-all"
            :label="trans('tables.filters.transactions.select_all')"
            class="mb-1 border-b border-theme-secondary-300 dark:border-theme-secondary-700"
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
