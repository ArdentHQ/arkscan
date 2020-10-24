<div id="transaction-list" class="w-full">
    <x-transactions.table-desktop :transactions="$transactions" />

    <x-transactions.list-mobile :transactions="$transactions" />

    <div class="pt-4 mt-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:mt-0 md:border-dashed">
        <a href="{{ route('transactions') }}" class="w-full button-secondary">@lang('actions.view_all')</a>
    </div>
</div>
