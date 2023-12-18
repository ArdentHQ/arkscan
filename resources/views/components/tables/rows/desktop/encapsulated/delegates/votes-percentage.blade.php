@props(['model'])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-dark-50">
    @if ($model)
        <x-percentage>{{ $model->votesPercentage() }}</x-percentage>
    @else
        <span>-</span>
    @endif
</x-tables.rows.desktop.encapsulated.cell>
