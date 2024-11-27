@props([
    'icon',
    'title',
    'amount',
    'fiat' => null,
    'duration' => trans('pages.statistics.gas.30_sec'),
])

<div class="flex flex-col flex-1 py-3 px-4 font-semibold rounded border md:rounded-xl border-theme-secondary-300 dark:border-theme-dark-700 dark:text-theme-dark-200">
    <div class="flex flex-1 justify-between items-center pb-3">
        <div class="flex items-center mb-0 space-x-1.5 text-sm text-theme-secondary-900 dark:text-theme-dark-50">
            <x-ark-icon :name="$icon" />

            <span>{{ $title }}</span>
        </div>

        {{ $duration }}
    </div>

    <div class="flex justify-between p-4 pt-3 -mx-4 -mb-3 text-sm rounded-b md:rounded-b-xl bg-theme-secondary-100 dark:bg-theme-dark-950">
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
