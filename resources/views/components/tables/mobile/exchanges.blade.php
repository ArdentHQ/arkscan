<div class="flex flex-col space-y-3 sm:space-y-4 table-list-mobile table-list-encapsulated">
    @foreach ($exchanges as $exchange)
        <div class="rounded border border-theme-secondary-300 dark:border-theme-secondary-800">
            <a class="flex justify-between items-center py-3 px-4 rounded-t dark:rounded-t-sm bg-theme-secondary-100 dark:bg-theme-secondary-800">
                <div class="flex items-center space-x-2">
                    <div class="flex justify-center items-center p-1.5 w-8 h-8 bg-white rounded-full border border-theme-secondary-200 dark:border-theme-secondary-900 dark:bg-theme-secondary-900">
                        <img class="max-w-full max-h-full" src="{{ config('arkscan.exchanges.icon_url') }}{{ $exchange->icon }}.svg" alt="{{ $exchange->name }} icon" />
                    </div>

                    <span class="text-sm font-semibold leading-4 text-theme-primary-600 dark:text-theme-secondary-200">{{ $exchange->name }}</span>
                </div>

                <x-ark-icon
                    name="arrows.arrow-external"
                    size="sm"
                    class="text-theme-secondary-500"
                />
            </a>

            <div class="flex flex-col px-4 pt-3 pb-4 space-y-4 sm:flex-row sm:justify-between sm:space-y-0">
                <div class="flex flex-col space-y-2 text-sm font-semibold">
                    <span class="text-theme-secondary-600">
                        @lang('general.exchange.top_pairs')
                    </span>

                    <x-exchanges.pairs :exchange="$exchange" />
                </div>

                <div class="flex flex-col space-y-2 text-sm font-semibold">
                    <span class="text-theme-secondary-600">
                        @lang('general.exchange.price')
                    </span>

                    @if ($exchange->price)
                        <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                            {{ ExplorerNumberFormatter::currency($exchange->price, 'USD', 4) }}
                        </span>
                    @else
                        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                            @lang('general.na')
                        </span>
                    @endif
                </div>

                <div class="flex flex-col space-y-2 text-sm font-semibold">
                    <span class="text-theme-secondary-600">
                        @lang('general.exchange.volume')
                    </span>

                    @if ($exchange->volume)
                        <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                            {{ ExplorerNumberFormatter::currency($exchange->volume, 'USD') }}
                        </span>
                    @else
                        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                            @lang('general.na')
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
