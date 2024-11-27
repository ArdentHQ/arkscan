@props([
    'icon',
    'title',
    'amount',
    'fiat' => null,
    'duration' => trans('pages.statistics.gas.30_sec'),
])

<div class="rounded md:rounded-xl px-4 py-3 border border-theme-secondary-300 dark:border-theme-dark-700 flex flex-1 flex-col font-semibold dark:text-theme-dark-200">
    <div class="flex flex-1 justify-between items-center pb-3">
        <div class="mb-0 text-sm text-theme-secondary-900 dark:text-theme-dark-50 flex space-x-1.5 items-center">
            <x-ark-icon :name="$icon" />

            <span>{{ $title }}</span>
        </div>

        {{ $duration }}
    </div>

    <div class="flex justify-between -mx-4 -mb-3 pt-3 p-4 text-sm rounded-b md:rounded-b-xl bg-theme-secondary-100 dark:bg-theme-dark-950">
        <span class="text-theme-secondary-900 dark:text-theme-dark-50">

        </span>

        <span>
            {{-- {{ (string) $amount }} --}}
            {{-- {{ ExplorerNumberFormatter::currency($amount, Network::currency()) }} --}}
            <x-general.amount
                :amount="$amount"
                {{-- :small-amount="0.00000000001" --}}
                hide-tooltip
            />
        </span>
    </div>
</div>
