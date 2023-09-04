@props(['model'])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-secondary-200">
    <x-percentage>{{ $model->votesPercentage() }}</x-percentage>
</x-tables.rows.desktop.encapsulated.cell>
