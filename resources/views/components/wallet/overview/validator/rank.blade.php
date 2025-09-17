@props(['wallet'])

<x-wallet.overview.item-entry
    :title="trans('pages.wallet.validator.rank')"
    :value="$wallet->rank()"
    :has-empty-value="! $wallet->isValidator()"
>
    <x-slot name="value">
        @if (! $wallet->isResigned() && ! $wallet->isDormant())
            <span>#{{ $wallet->rank() }}</span>
            <span>/</span>
        @endif

        @if($wallet->isDormant())
            <div class="space-x-2 flex items-center">
                <span class="text-theme-secondary-700 dark:text-theme-dark-500">
                    @lang('pages.validators.dormant')
                </span>

                <x-ark-info
                    :tooltip="trans('pages.validators.dormant_tooltip')"
                    type="info"
                />
            </div>
        @elseif($wallet->isResigned())
            <span class="text-theme-danger-700 dark:text-theme-danger-400">
                @lang('pages.validators.resigned')
            </span>
        @elseif($wallet->rank() > Network::validatorCount())
            <span class="text-theme-secondary-500 dark:text-theme-dark-500">
                @lang('pages.validators.standby')
            </span>
        @else
            <span class="text-theme-success-700 dark:text-theme-success-500">
                @lang('pages.validators.active')
            </span>
        @endif
    </x-slot>
</x-wallet.overview.item-entry>
