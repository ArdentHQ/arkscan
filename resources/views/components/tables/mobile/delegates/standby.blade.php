<div class="w-full table-container md:hidden">
    <table>
        <thead>
            <tr>
                <x-tables.headers.mobile.number name="general.delegates.rank" />
                <x-tables.headers.mobile.text name="general.delegates.name" />
                <x-tables.headers.mobile.number name="general.delegates.votes" alignment="sm:text-left" />
            </tr>
        </thead>
        <tbody>
            @foreach ($delegates as $delegate)
            <x-ark-tables.row>
                <x-ark-tables.cell>
                    <x-tables.rows.mobile.rank :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <x-tables.rows.mobile.username-with-avatar :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <x-tables.rows.mobile.votes :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
            @endforeach
        </tbody>
    </table>
</div>
