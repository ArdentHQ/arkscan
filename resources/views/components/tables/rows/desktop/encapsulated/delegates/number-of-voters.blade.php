@props([
    'model',
    'withoutBreakdown' => false,
])

<x-tables.rows.desktop.encapsulated.cell class="text-theme-secondary-900 dark:text-theme-dark-200">
    @if ($model)
        <x-number>{{ $model->voterCount() }}</x-number>

        @unless($withoutBreakdown)
            <div class="hidden mt-1 space-x-2 text-xs divide-x sm:flex lg:hidden divide divide-theme-secondary-300 text-theme-secondary-700 leading-3.75 dark:text-theme-dark-200 dark:divide-theme-dark-700">
                <div>{{ number_format($model->votes()) }}</div>

                <div class="pl-2">
                    <x-percentage>{{ $model->votePercentage() }}</x-percentage>
                </div>
            </div>
        @endunless
    @else
        <span>-</span>
    @endif
</x-tables.rows.desktop.encapsulated.cell>
