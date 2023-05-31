@props(['transaction'])

<div class="flex items-center space-x-2 text-xs">
    <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
        <div class="md:hidden">
            @lang('general.search.type')
        </div>

        <x-ark-icon
            name="app-transactions.{{ $transaction->iconType() }}"
            class="hidden md:block"
        />
    </div>

    <div class="text-theme-secondary-700 dark:text-theme-secondary-500">
        @lang('general.transaction.types.'.$transaction->typeName())
    </div>
</div>
