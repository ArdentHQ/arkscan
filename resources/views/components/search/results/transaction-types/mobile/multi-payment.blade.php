@props(['transaction'])

<div class="flex flex-col space-y-2">
    <div class="flex items-center space-x-2 text-xs">
        <x-general.encapsulated.transaction-direction-badge>
            @lang('general.search.from')
        </x-general.encapsulated.transaction-direction-badge>

        <x-general.identity
            :model="$transaction->sender()"
            without-link
            class="text-theme-secondary-900 dark:text-theme-dark-50"
        />
    </div>

    <div class="flex items-center space-x-2 text-xs">
        <x-general.encapsulated.transaction-direction-badge>
            @lang('general.search.to')
        </x-general.encapsulated.transaction-direction-badge>

        <span class="text-theme-secondary-900 dark:text-theme-dark-50">
            @lang('tables.transactions.multiple')

            ({{ $transaction->recipientsCount() }})
        </span>
    </div>
</div>
