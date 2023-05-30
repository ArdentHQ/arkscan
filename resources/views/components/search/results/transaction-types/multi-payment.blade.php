@props(['transaction'])

<div class="flex items-center space-x-2 text-xs">
    <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
        <x-ark-icon name="app-transactions.{{ $transaction->iconType() }}" />
    </div>

    <div class="text-theme-secondary-700 dark:text-theme-secondary-500">
        @lang('general.transaction.types.'.$transaction->typeName())

        {{ count($transaction->payments()) }}
    </div>
</div>
