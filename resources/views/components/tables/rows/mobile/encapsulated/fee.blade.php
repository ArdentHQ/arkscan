@props(['model'])

<div class="sm:flex sm:flex-col sm:justify-center space-y-2">
    <div class="text-sm font-semibold dark:text-theme-secondary-500">
        @lang('tables.transactions.fee', ['currency' => Network::currency()])
    </div>

    <x-tables.rows.desktop.encapsulated.fee :model="$model" />
</div>
