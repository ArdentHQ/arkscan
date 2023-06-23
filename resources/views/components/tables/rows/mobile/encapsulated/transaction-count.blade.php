@props([
    'model',
    'valueClass'    => null,
    'flexDirection' => 'space-y-2 sm:flex-col sm:justify-center',
])

<div {{ $attributes->class(['sm:flex', $flexDirection]) }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        @lang('tables.blocks.transactions')
    </div>

    <div @class([
        'inline-block font-semibold text-theme-secondary-900 dark:text-theme-secondary-50',
        $valueClass,
    ])>
        <x-tables.rows.desktop.encapsulated.transaction-count :model="$model" />
    </div>
</div>
