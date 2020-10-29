<div id="transaction-list" class="w-full">
    @if($transactions->isEmpty())
        <div wire:poll="pollTransactions">
            <x-transactions.table-desktop-skeleton />

            <x-transactions.table-mobile-skeleton />
        </div>
    @else
        <div wire:poll.{{ Network::blockTime() }}s="pollTransactions">
            <x-transactions.table-desktop :transactions="$transactions" />

            <x-transactions.table-mobile :transactions="$transactions" />

            <div class="pt-4 mt-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:mt-0 md:border-dashed">
                <a href="{{ route('transactions') }}" class="w-full button-secondary">@lang('actions.view_all')</a>
            </div>
        </div>
    @endif
</div>
