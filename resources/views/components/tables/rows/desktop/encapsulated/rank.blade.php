@props([
    'index',
    'results'
])

<x-tables.rows.desktop.encapsulated.cell>
    {{ $index + ($results->currentPage() - 1) * $results->perPage() }}
</x-tables.rows.desktop.encapsulated.cell>
