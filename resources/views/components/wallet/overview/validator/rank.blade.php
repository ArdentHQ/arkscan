@props(['wallet'])

<x-wallet.overview.item-entry
    :title="trans('pages.wallet.validator.rank')"
    :value="$wallet->rank()"
    :has-empty-value="! $wallet->isValidator()"
>
    <x-slot name="value">
        @unless($wallet->isResigned())
            <span>#{{ $wallet->rank() }}</span>
            <span>/</span>
        @endunless

        @if($wallet->isResigned())
            <span class="text-theme-danger-700 dark:text-theme-danger-400">
                @lang('pages.validators.resigned')
            </span>
        @elseif($wallet->rank() > Network::validatorCount())
            <span class="text-theme-secondary-500 dark:text-theme-dark-700">
                @lang('pages.validators.standby')
            </span>
        @else
            <span class="text-theme-success-700 dark:text-theme-success-500">
                @lang('pages.validators.active')
            </span>
        @endif
    </x-slot>
</x-wallet.overview.item-entry>
