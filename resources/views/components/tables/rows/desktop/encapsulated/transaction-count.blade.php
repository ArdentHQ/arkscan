@props([
    'model',
])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-dark-50">
    {{ $model->transactionCount() }}
</x-tables.rows.desktop.encapsulated.cell>
