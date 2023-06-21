@props(['exchange'])

<div class="flex flex-col flex-1 space-y-2 text-sm font-semibold">
    <span class="text-theme-secondary-600 dark:text-theme-secondary-500">
        @lang('general.exchange.top_pairs')
    </span>

    <x-exchanges.pairs :exchange="$exchange" />
</div>
