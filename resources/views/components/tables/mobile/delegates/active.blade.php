<div class="w-full table-container md:hidden">
    <table>
        <thead>
            <tr>
                <x-tables.headers.mobile.number name="general.delegates.rank" alignment="text-left" />
                <x-tables.headers.mobile.text name="general.delegates.name" />
                <x-tables.headers.mobile.status name="general.delegates.status" alignment="text-right"/>
            </tr>
        </thead>
        <tbody>
            @foreach ($delegates as $delegate)
                <x-ark-tables.row
                    :danger="$delegate->keepsMissing()"
                    :warning="$delegate->justMissed()"
                >
                    <x-ark-tables.cell>
                        <x-tables.rows.mobile.rank :model="$delegate" />
                    </x-ark-tables.cell>
                    <x-ark-tables.cell>
                        <x-tables.rows.mobile.username-with-avatar :model="$delegate" />
                    </x-ark-tables.cell>
                    <x-ark-tables.cell>
                        <x-tables.rows.mobile.round-status-history :model="$delegate" />
                    </x-ark-tables.cell>
                </x-ark-tables.row>
            @endforeach
        </tbody>
    </table>
</div>
