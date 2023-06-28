@props(['transactions'])

<x-tables.toolbars.toolbar :result-count="$transactions->total()">
    <div class="flex space-x-3">
        <livewire:modals.export-transactions />

        <x-tables.filters.transactions />
    </div>
</x-general.encapsulated.table-header>
