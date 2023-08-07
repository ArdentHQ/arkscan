@props([
    'model',
])

<x-tables.rows.desktop.encapsulated.cell>
    {{ $model->transactionCount() }}
</x-tables.rows.desktop.encapsulated.cell>
