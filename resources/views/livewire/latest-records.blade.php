<div x-data="{
    tabsOpen: false,
    selected: 'transactions',
    transactionTypeFilter: '{{ $state["type"] }}',
    transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $state['type'])',
}" x-cloak class="w-full">

    <x-latest-records-tabs :transaction-type="$state['type']" :selected="$state['selected']" />

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

                    @if(count($blocks) === 15)
                        <a href="{{ route('blocks', ['page' => 2]) }}" class="mt-4 w-full button-secondary">@lang('actions.view_all')</a>
                    @endif
                </div>
            @endif
        </div>
    @else
        <div id="transaction-list" class="w-full">
            @if($transactions->isEmpty())
                @if($state['type'] !== 'all')
                    <div wire:poll.{{ Network::blockTime() }}s="pollTransactions" wire:key="poll_transactions_empty">
                        <x-general.no-results :text="trans('pages.home.no_transaction_results', [trans('forms.search.transaction_types.'.$state['type'])])" />
                    </div>
                @else
                    <div wire:init="pollTransactions" wire:key="poll_transactions_skeleton">
                        <x-tables.desktop.skeleton.transactions />

                        <x-tables.mobile.skeleton.transactions />
                    </div>
                @endif
            @else
                <div wire:poll.{{ Network::blockTime() }}s="pollTransactions" wire:key="poll_transactions_real">
                    <x-tables.desktop.transactions :transactions="$transactions" />

                    <x-tables.mobile.transactions :transactions="$transactions" :state="[$state['type']]" />

                    @if(count($transactions) === 15)
                        <a href="{{ route('transactions', ['page' => 2, 'state[type]' => $state['type']]) }}" class="mt-4 w-full button-secondary">@lang('actions.view_all')</a>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
