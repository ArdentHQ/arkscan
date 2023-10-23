@props(['transaction'])

<div class="flex flex-col space-y-2">
    <div class="flex items-center space-x-2 text-xs">
        <x-general.encapsulated.transaction-direction-badge>
            @lang('general.search.from')
        </x-general.encapsulated.transaction-direction-badge>

        <x-general.identity
            :model="$transaction->isUnvote() ? $transaction->unvoted() : $transaction->voted()"
            without-reverse
            without-reverse-class="space-x-2"
            without-link
            without-icon
            class="text-theme-secondary-900 dark:text-theme-dark-50"
        />
    </div>

    <div class="flex items-center space-x-2 text-xs">
        <x-general.encapsulated.transaction-direction-badge>
            @lang('general.search.to')
        </x-general.encapsulated.transaction-direction-badge>

        @if ($transaction->isVoteCombination())
            <x-general.identity
                :model="$transaction->voted()"
                without-reverse
                without-reverse-class="space-x-2"
                without-link
                without-icon
                class="text-theme-secondary-900 dark:text-theme-dark-50"
            />
        @else
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                @lang('general.search.contract')
            </span>
        @endif
    </div>
</div>
