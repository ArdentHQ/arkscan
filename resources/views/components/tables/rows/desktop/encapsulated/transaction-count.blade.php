@props([
    'model',
])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-secondary-200">
    {{ $model->transactionCount() }}
</x-tables.rows.desktop.encapsulated.cell>
