@props(['model'])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-secondary-200">
    <x-number>{{ $model->voterCount() }}</x-number>

    <div class="hidden sm:flex lg:hidden text-xs dark:text-theme-dark-200 space-x-2 divide divide-x divide-theme-secondary-300 dark:divide-theme-dark-700 mt-1">
        <div>{{ number_format($model->votes()) }}</div>

        <div class="pl-2">
            <x-percentage>{{ $model->votePercentage() }}</x-percentage>
        </div>
    </div>
</x-tables.rows.desktop.encapsulated.cell>
