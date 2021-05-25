<x-ark-tables.table class="w-full md:hidden">
    <thead>
        <tr>
            <x-tables.headers.mobile.text name="general.delegates.name" />
            <x-tables.headers.mobile.number name="general.delegates.votes" />
        </tr>
    </thead>
    <tbody>
        @foreach ($delegates as $delegate)
        <x-ark-tables.row>
            <x-ark-tables.cell>
                <x-tables.rows.mobile.username-with-avatar :model="$delegate" />
            </x-ark-tables.cell>
            <x-ark-tables.cell>
                <x-tables.rows.mobile.votes :model="$delegate" />
            </x-ark-tables.cell>
        </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
