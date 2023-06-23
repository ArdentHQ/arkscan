<x-tables.mobile.includes.encapsulated>
    @foreach ($exchanges as $exchange)
        <x-tables.rows.mobile>
            <x-slot name="header" padding="p-0">
                <x-tables.headers.mobile.encapsulated.exchange-link :exchange="$exchange" />
            </x-slot>

            <x-tables.rows.mobile.encapsulated.exchanges.top-pairs :exchange="$exchange" />

            <x-tables.rows.mobile.encapsulated.exchanges.price :exchange="$exchange" />

            <x-tables.rows.mobile.encapsulated.exchanges.volume :exchange="$exchange" />
        </x-tables.rows.mobile>
    @endforeach
</x-tables.mobile.includes.encapsulated>
