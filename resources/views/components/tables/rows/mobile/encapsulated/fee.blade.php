@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center leading-4.25') }}>
    <div class="text-sm font-semibold leading-4.25 dark:text-theme-dark-500">
        @lang('tables.transactions.fee', ['currency' => Network::currency()])
    </div>

    <div class="inline-block leading-4.25">
        <x-tables.rows.desktop.encapsulated.fee :model="$model" />
    </div>
</div>
