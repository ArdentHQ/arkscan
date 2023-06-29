@props(['transactions'])

<x-tables.toolbars.toolbar :result-count="$transactions->total()">
    <div class="flex space-x-3">
        <livewire:modals.export-transactions />

        <div class="flex-1">
            <x-tables.filters.transactions />
        </div>
    </div>
</x-general.encapsulated.table-header>
