@props([
    'icon',
    'title',
    'amount',
    'duration',
])

@php ($canBeExchanged = Network::canBeExchanged())

<div class="flex flex-col flex-1 py-3 px-4 font-semibold bg-white rounded border border-white md:rounded-lg dark:border-theme-dark-900 dark:text-theme-dark-200 dark:bg-theme-dark-900">
    <div class="flex flex-1 justify-between items-center pb-3">
        <div class="flex items-center mb-0 space-x-1.5 text-sm text-theme-secondary-900 dark:text-theme-dark-50">
            <x-ark-icon :name="$icon" />

            <span>{{ $title }}</span>
        </div>

        {{ trans_choice('general.seconds_duration', $duration, ['duration' => $duration]) }}
    </div>

    <div @class([
        'flex p-4 pt-3 -mx-4 -mb-3 text-sm rounded-b md:rounded-b-lg bg-theme-secondary-100 dark:bg-theme-dark-950',
        'justify-between' => $canBeExchanged,
        'justify-end' => ! $canBeExchanged,
    ])>
        @if ($canBeExchanged)
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                ~ {{ ExchangeRate::convert($amount) }}
            </span>
        @endif

        <span data-tippy-content="{{ ExplorerNumberFormatter::gweiToArk($amount) }}">
            {{ (string) $amount }}

            @lang('general.gwei')
        </span>
    </div>
</div>
