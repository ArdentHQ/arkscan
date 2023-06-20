@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        @lang('tables.blocks.transactions')
    </div>

    <div class="inline-block font-semibold text-theme-secondary-900 dark:text-theme-secondary-50">
        <x-tables.rows.desktop.encapsulated.transaction-count :model="$model" />
    </div>
</div>
