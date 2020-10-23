<div x-data="{
    dropdownOpen: false,
    selected: 'transactions',
    transactionTypeFilter: 'all',
    transactionTypeFilterLabel: 'All',
}" x-cloak class="w-full">
    <x-transaction-table-filter />

    <div class="hidden tabs md:flex">
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': selected === 'transactions' }"
            @click="selected = 'transactions'"
        >
            @lang('pages.home.latest_transactions')
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': selected === 'blocks' }"
            @click="selected = 'blocks'"
        >
            @lang('pages.home.latest_blocks')
        </div>
    </div>

    <div class="md:hidden">
        <x-ark-dropdown
            wrapper-class="relative w-full p-2 mb-8 border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800"
            button-class="w-full font-semibold text-left text-theme-secondary-900 dark:text-theme-secondary-200"
            dropdown-classes="left-0 w-full z-20"
            :init-alpine="false"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4">
                    @svg('menu-open', 'h-4 w-4')

                    <div x-show="selected === 'transactions'">@lang('pages.home.latest_transactions')</div>
                    <div x-show="selected === 'blocks'">@lang('pages.home.latest_blocks')</div>
                </div>
            </x-slot>

            <div class="p-4">
                <a @click="selected = 'transactions'" class="dropdown-entry">
                    @lang('pages.home.latest_transactions')
                </a>

                <a @click="selected = 'blocks'" class="dropdown-entry">
                    @lang('pages.home.latest_blocks')
                </a>
            </div>
        </x-ark-dropdown>
    </div>

    <div x-show="selected === 'transactions'">
        <livewire:transaction-table view-more />
    </div>

    <div x-show="selected === 'blocks'">
        <livewire:block-table view-more />
    </div>
</div>
