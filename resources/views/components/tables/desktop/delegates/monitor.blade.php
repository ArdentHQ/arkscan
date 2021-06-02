<x-ark-tables.table sticky class="hidden w-full md:block">
    <thead>
        <tr>
            <x-tables.headers.desktop.text name="pages.delegates.order" alignment="text-left" />
            <x-tables.headers.desktop.address name="pages.delegates.name" />
            <x-tables.headers.desktop.text name="pages.delegates.forging_at" alignment="text-left" />
            <x-tables.headers.desktop.status name="pages.delegates.status" />
            <x-tables.headers.desktop.text name="pages.delegates.block_id" />
        </tr>
    </thead>
    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row
                wire:key="{{ $delegate->publicKey() }}-{{ $round }}"
                :danger="$delegate->keepsMissing()"
                :warning="$delegate->justMissed()"
            >
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.slot-id :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell wire:key="{{ $delegate->publicKey() }}-username-desktop">
                    <x-tables.rows.desktop.username-with-avatar :model="$delegate->wallet()" />
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.slot-time :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell wire:key="{{ $delegate->publicKey() }}-round-status-{{ $delegate->status() }}-desktop">
                    <x-tables.rows.desktop.round-status :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right">
                    <x-tables.rows.desktop.wallet-last-block :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
