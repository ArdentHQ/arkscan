@props(['transaction'])

<div class="flex items-center space-x-2 text-xs">
    <div class="flex items-center text-theme-secondary-500 dark:text-theme-dark-200">
        <div class="mr-2 md:hidden">
            @lang('general.search.type')
        </div>

        <x-ark-icon name="app-transactions.{{ $transaction->iconType() }}" />
    </div>

    <div class="text-theme-secondary-700 dark:text-theme-dark-50">
        @lang('general.transaction.types.'.$transaction->typeName())

        {{ count($transaction->payments()) }}
    </div>
</div>
