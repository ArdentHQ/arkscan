@props([
    'transactions',
    'wallet',
])

<x-tables.toolbars.toolbar :result-count="$transactions->total()">
    <div class="flex space-x-3">
        <livewire:modals.export-transactions :wallet="$wallet" />

        <div class="flex-1">
            <x-tables.filters.transactions />

            <x-tables.filters.transactions mobile />
        </div>
    </div>
</x-general.encapsulated.table-header>
