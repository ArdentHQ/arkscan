@props(['exchanges'])

<x-tables.encapsulated-table
    x-data="TableSorting('header-volume', 'desc')"
    class="hidden w-full rounded-b-xl md:block"
    sticky
>
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.text
                name="general.exchange.name"
                sorting-id="header-name"
            />
            <x-tables.headers.desktop.text
                name="general.exchange.top_pairs"
                sorting-id="header-top_pairs"
            />

            <x-tables.headers.desktop.number
                name="general.exchange.price"
                initial-sort="desc"
                sorting-id="header-price"
            >
                <span>({{ config('currencies.usd.currency') }})</span>
            </x-tables.headers.desktop.number>

            <x-tables.headers.desktop.number
                name="general.exchange.volume"
                class="text-right"
                breakpoint="md-lg"
                responsive
                initial-sort="desc"
                sorting-id="header-volume"
            >
                <span>({{ config('currencies.usd.currency') }})</span>
            </x-tables.headers.desktop.number>
        </tr>
    </thead>

    <tbody x-ref="tbody">
        @foreach($exchanges as $exchange)
            <x-ark-tables.row
                wire:key="exchange-{{ $exchange->name }}"
                class="font-semibold"
            >
                <x-ark-tables.cell class="text-sm">
                    <div class="flex items-center space-x-3">
                        <div class="flex justify-center items-center w-5 h-5">
                            <img class="max-w-full max-h-full" src="{{ config('explorer.exchanges.icon_url') }}{{ $exchange->icon }}.svg" alt="{{ $exchange->name }} icon" />
                        </div>

                        <x-ark-external-link
                            :url="$exchange->url"
                            :text="$exchange->name"
                            class="flex items-center space-x-2 font-semibold leading-4 break-words link"
                        />
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-sm">
                    <x-exchanges.pairs :exchange="$exchange" />
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-sm text-right"
                    data-value="{{ $exchange->price }}"
                >
                    @if ($exchange->price)
                        <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                            {{ ExplorerNumberFormatter::usdWithDecimals($exchange->price) }}
                        </span>
                    @else
                        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                            @lang('general.na')
                        </span>
                    @endif
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-sm text-right"
                    breakpoint="md-lg"
                    data-value="{{ $exchange->volume }}"
                    responsive
                >
                    @if ($exchange->volume)
                        {{ ExplorerNumberFormatter::usdWithDecimals($exchange->volume, 2) }}
                    @else
                        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                            @lang('general.na')
                        </span>
                    @endif
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
