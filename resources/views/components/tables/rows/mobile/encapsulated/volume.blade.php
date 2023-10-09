@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center leading-4.25') }}>
    <div class="text-sm font-semibold whitespace-nowrap leading-4.25 dark:text-theme-secondary-500">
        @lang('tables.blocks.volume', ['currency' => Network::currency()])
    </div>

    <div class="inline-block font-semibold text-theme-secondary-900 dark:text-theme-secondary-50 leading-4.25">
        <x-tables.rows.desktop.encapsulated.volume :model="$model" />
    </div>
</div>
