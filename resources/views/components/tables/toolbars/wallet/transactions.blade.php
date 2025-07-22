@props([
    'transactions',
    'wallet',
])

<x-tables.toolbars.toolbar :result-count="$transactions->total()">
    <div class="flex space-x-3">
        <livewire:modals.export-transactions :wallet="$wallet" />

        <div class="flex-1">
            <x-tables.filters.wallet.transactions />

            <x-tables.filters.wallet.transactions mobile />
        </div>
    </div>
</x-tables.toolbars.toolbar>
