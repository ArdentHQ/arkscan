@props(['model'])

<div {{ $attributes->class('space-y-2 sm:flex sm:flex-col sm:justify-center') }}>
    <div class="text-sm font-semibold dark:text-theme-secondary-500 leading-[17px]">
        @lang('tables.transactions.fee', ['currency' => Network::currency()])
    </div>

    <x-tables.rows.desktop.encapsulated.fee :model="$model" />
</div>
