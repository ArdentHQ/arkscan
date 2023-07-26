<div wire:init="setIsReady">
    <x-tables.desktop.block-transactions
        :transactions="$transactions"
        with-lazy-loading
    />

    <div class="px-3 sm:px-0">
        <x-tables.mobile.block-transactions :transactions="$transactions" />
    </div>

    <x-general.pagination.lazy-loading />
</div>
