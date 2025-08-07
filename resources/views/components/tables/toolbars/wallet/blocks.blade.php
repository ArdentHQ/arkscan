@props([
    'blocks',
    'wallet',
])

<x-tables.toolbars.toolbar :result-count="$blocks->total()">
    <livewire:modals.export-blocks
        :wallet="$wallet"
    />
</x-tables.toolbars.toolbar>
