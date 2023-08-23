@props(['model'])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-secondary-200">
    {{ number_format($model->votes()) }}
</x-tables.rows.desktop.encapsulated.cell>
