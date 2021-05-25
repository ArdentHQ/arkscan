<x-ark-tables.table class="w-full md:hidden">
    <thead>
        <tr>
            <x-tables.headers.mobile.number name="pages.delegates.order" alignment="text-left" />
            <x-tables.headers.mobile.address name="pages.delegates.name" />
            <x-tables.headers.mobile.text name="pages.delegates.forging_at" alignment="text-left" responsive/>
            <x-tables.headers.mobile.status name="pages.delegates.status" alignment="text-left" />
        </tr>
    </thead>
    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row
                wire:key="$delegate->publicKey()"
                :danger="$delegate->keepsMissing()"
                :warning="$delegate->justMissed()"
            >
                <x-ark-tables.cell>
                    <x-tables.rows.mobile.slot-id :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell wire:key="{{ $delegate->publicKey() }}-username-mobile">
                    <x-tables.rows.mobile.username-with-avatar :model="$delegate->wallet()" />
                </x-ark-tables.cell>

                <x-ark-tables.cell responsive breakpoint="sm">
                    <x-tables.rows.mobile.slot-time :model="$delegate" />
                </x-ark-tables.cell>

                <x-ark-tables.cell wire:key="{{ $delegate->publicKey() }}-round-status-{{ $delegate->status() }}-mobile">
                    <x-tables.rows.mobile.round-status :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
