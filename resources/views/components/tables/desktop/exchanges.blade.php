@props(['exchanges'])

<x-tables.encapsulated-table sticky class="hidden mt-6 w-full rounded-b-xl md:block">
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.text name="general.exchange.name" />
            <x-tables.headers.desktop.text name="general.exchange.top_pairs" />

            <x-tables.headers.desktop.number name="general.exchange.price">
                <span>({{ Settings::currency()}})</span>
            </x-tables.headers.desktop.number>

            <x-tables.headers.desktop.number
                name="general.exchange.volume"
                class="text-right"
                breakpoint="md-lg"
                responsive
            >
                <span>({{ Settings::currency()}})</span>
            </x-tables.headers.desktop.number>
        </tr>
    </thead>

    <tbody>
        @foreach($exchanges as $exchange)
            <x-ark-tables.row
                wire:key="exchange-{{ $exchange['name'] }}"
                class="font-semibold"
            >
                <x-ark-tables.cell>
                    <x-ark-external-link
                        :url="$exchange['url']"
                        :text="$exchange['name']"
                        icon-class="dark:text-theme-secondary-700"
                    />
                </x-ark-tables.cell>

                <x-ark-tables.cell>
                    <div class="flex space-x-2 font-semibold divide-x divide-theme-secondary-300 text-theme-secondary-900 dark:divide-theme-secondary-800 dark:text-theme-secondary-200">
                        @foreach ($exchange['pairs'] as $pair)
                            <div class="pl-2 first:pl-0">
                                {{ $pair }}
                            </div>
                        @endforeach
                    </div>
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    @if ($exchange['price'])
                        {{ $exchange['price'] }}
                    @else
                        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                            @lang('general.na')
                        </span>
                    @endif
                </x-ark-tables.cell>

                <x-ark-tables.cell
                    class="text-right"
                    breakpoint="md-lg"
                    responsive
                >
                    @if ($exchange['volume'])
                        {{ $exchange['volume'] }}
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
