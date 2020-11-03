<div x-data="{
    tabsOpen: false,
    selected: 'transactions',
    transactionTypeFilter: 'all',
    transactionTypeFilterLabel: 'All',
}" x-cloak class="w-full">
    <div class="w-full md:mb-8">
        <div class="relative flex flex-col justify-between md:items-end md:flex-row md:justify-start">
            <h2 class="mb-8 text-3xl md:mb-0 sm:text-4xl">@lang('pages.home.transactions_and_blocks')</h2>

            <div x-show="selected === 'transactions'">
                <x-transaction-table-filter />
            </div>
        </div>
    </div>

    <div class="hidden tabs md:flex">
        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': selected === 'transactions' }"
            wire:click="$set('state.selected', 'transactions')"
            @click="selected = 'transactions'"
        >
            @lang('pages.home.latest_transactions')
        </div>

        <div
            class="tab-item transition-default"
            :class="{ 'tab-item-current': selected === 'blocks' }"
            wire:click="$set('state.selected', 'blocks')"
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
            dropdown-property="tabsOpen"
        >
            <x-slot name="button">
                <div class="flex items-center space-x-4">
                    <x-icon name="menu-open" size="sm" />

                    <div x-show="selected === 'transactions'">@lang('pages.home.latest_transactions')</div>
                    <div x-show="selected === 'blocks'">@lang('pages.home.latest_blocks')</div>
                </div>
            </x-slot>

            <div class="p-4">
                <a wire:click="$set('state.selected', 'transactions')" @click="selected = 'transactions'" class="dropdown-entry">
                    @lang('pages.home.latest_transactions')
                </a>

                <a wire:click="$set('state.selected', 'blocks')" @click="selected = 'blocks'" class="dropdown-entry">
                    @lang('pages.home.latest_blocks')
                </a>
            </div>
        </x-ark-dropdown>
    </div>

    @if($state['selected'] === 'blocks')
        <div id="block-list" class="w-full">
            @if($blocks->isEmpty())
                <div wire:poll="pollBlocks" wire:key="poll_blocks_skeleton">
                    <x-tables.desktop.skeleton.blocks />

                    <x-tables.mobile.skeleton.blocks />
                </div>
            @else
                <div wire:poll.{{ Network::blockTime() }}s="pollBlocks" wire:key="poll_blocks_real">
                    <x-tables.desktop.blocks :blocks="$blocks" />

                    <x-tables.mobile.blocks :blocks="$blocks" />

                    <div class="pt-4 mt-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:mt-0 md:border-dashed">
                        <a href="{{ route('blocks') }}" class="w-full button-secondary">@lang('actions.view_all')</a>
                    </div>
                </div>
            @endif
        </div>
    @else
        <div id="transaction-list" class="w-full">
            @if($transactions->isEmpty())
                <div wire:poll="pollTransactions" wire:key="poll_transactions_skeleton">
                    <x-tables.desktop.skeleton.transactions />

                    <x-tables.mobile.skeleton.transactions />
                </div>
            @else
                <div wire:poll.{{ Network::blockTime() }}s="pollTransactions" wire:key="poll_transactions_real">
                    <x-tables.desktop.transactions :transactions="$transactions" />

                    <x-tables.mobile.transactions :transactions="$transactions" />

                    <div class="pt-4 mt-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:mt-0 md:border-dashed">
                        <a href="{{ route('transactions') }}" class="w-full button-secondary">@lang('actions.view_all')</a>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
