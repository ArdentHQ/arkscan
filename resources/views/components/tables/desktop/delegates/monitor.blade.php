<x-ark-tables.table sticky class="w-full">
    <thead>
        <tr>
            <x-tables.headers.desktop.text name="pages.delegates.order" />
            <x-tables.headers.desktop.address name="pages.delegates.name" />
            <x-tables.headers.desktop.text name="pages.delegates.forging_at" responsive breakpoint="sm" />
            <x-tables.headers.desktop.status name="pages.delegates.status" last-on="md"  />
            <x-tables.headers.desktop.text
                name="pages.delegates.block_id"
                responsive
                breakpoint="md"
                class="text-right"
            />
        </tr>
    </thead>
    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row
                wire:key="{{ Helpers::generateId($delegate->publicKey(), $round, $delegate->status()) }}"
                :danger="$delegate->keepsMissing()"
                :warning="$delegate->justMissed()"
            >
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.slot-id :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <span class="hidden md:inline">
                        <x-tables.rows.desktop.username :model="$delegate->wallet()" />
                    </span>
                    <span class="md:hidden">
                        <x-tables.rows.mobile.username-with-avatar :model="$delegate->wallet()" />
                    </span>
                </x-ark-tables.cell>
                <x-ark-tables.cell responsive breakpoint="sm">
                    <x-tables.rows.desktop.slot-time :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell last-on="md">
                    <x-tables.rows.desktop.round-status :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right" responsive breakpoint="md" >
                    <x-tables.rows.desktop.wallet-last-block :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
