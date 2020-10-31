<div id="transaction-list" class="w-full">
    @if($transactions->isEmpty())
        <div wire:poll="pollTransactions">
            <x-tables.desktop.skeleton.transactions />

            <x-tables.mobile.skeleton.transactions />
        </div>
    @else
        <div wire:poll.{{ Network::blockTime() }}s="pollTransactions">
            <x-tables.desktop.transactions :transactions="$transactions" />

            <x-tables.mobile.transactions :transactions="$transactions" />

            <div class="pt-4 mt-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:mt-0 md:border-dashed">
                <a href="{{ route('transactions') }}" class="w-full button-secondary">@lang('actions.view_all')</a>
            </div>
        </div>
    @endif
</div>
