<div x-data="{
    tabsOpen: false,
    selected: 'transactions',
    transactionTypeFilter: '{{ $state["type"] }}',
    transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $state['type'])',
}" x-cloak class="w-full">

    @if($state['selected'] === 'blocks')
        <div id="block-list" class="w-full">
            @if($blocks->isEmpty())
                <div wire:poll="pollBlocks" wire:key="poll_blocks_skeleton">
                    <x-tables.desktop.skeleton.home-blocks />

                    <x-tables.mobile.skeleton.home-blocks />
                </div>
            @else
                <div wire:poll.{{ Network::blockTime() }}s="pollBlocks" wire:key="poll_blocks_real">
                    <x-tables.desktop.home-blocks :blocks="$blocks" />

                    <x-tables.mobile.home-blocks :blocks="$blocks" />

                    @if(count($blocks) === 15)
                        <a href="{{ route('blocks', ['page' => 2]) }}" class="mt-4 w-full button-secondary">@lang('actions.view_all')</a>
                    @endif
                </div>
            @endif
        </div>
    @endif
</div>
