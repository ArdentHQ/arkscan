@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold leading-[17px] dark:text-theme-secondary-500">
        @lang('tables.transactions.fee', ['currency' => Network::currency()])
    </div>

    <x-tables.rows.desktop.encapsulated.fee :model="$model" />
</div>
