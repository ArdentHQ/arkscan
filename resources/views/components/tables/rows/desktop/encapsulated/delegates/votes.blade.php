@props(['model'])

@php ($votes = $model->votes())

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-secondary-200">
    @if ($votes > 0 && $votes < 0.01)
        <span data-tippy-content="{{ ExplorerNumberFormatter::unformattedRawValue($votes) }}">
            &lt;0.01
        </span>
    @else
        {{ number_format($votes, 2) }}
    @endif
</x-tables.rows.desktop.encapsulated.cell>
