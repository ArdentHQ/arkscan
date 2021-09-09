<x-ark-tables.table sticky class="w-full">
    <thead>
        <tr>
            <x-tables.headers.desktop.text name="general.delegates.rank" />
            <x-tables.headers.desktop.address name="general.delegates.name" />
            <x-tables.headers.desktop.status name="general.delegates.status" last-on="md">
                <x-ark-info :tooltip="trans('pages.delegates.info.status')" />
            </x-tables.headers.desktop.status>
            <x-tables.headers.desktop.number name="general.delegates.votes" responsive />
            <x-tables.headers.desktop.number name="general.delegates.productivity" responsive breakpoint="md">
                <x-ark-info :tooltip="trans('pages.delegates.info.productivity')" />
            </x-tables.headers.desktop.number>
        </tr>
    </thead>
    <tbody>
        @foreach($delegates as $delegate)
            <x-ark-tables.row
                wire:key="{{ Helpers::generateId($delegate->username(), $delegate->rank()) }}"
                :danger="$delegate->keepsMissing()"
                :warning="$delegate->justMissed()"
            >
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.rank :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell>
                    <span class="hidden md:inline">
                        <x-tables.rows.desktop.username :model="$delegate" />
                    </span>
                    <span class="md:hidden">
                        <x-tables.rows.mobile.username-with-avatar :model="$delegate" />
                    </span>
                </x-ark-tables.cell>
                <x-ark-tables.cell last-on="md">
                    <x-tables.rows.desktop.round-status-history :model="$delegate" />
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right" responsive>
                    <span class="hidden sm:inline">
                        <x-tables.rows.desktop.votes :model="$delegate" />
                    </span>
                    <span class="sm:hidden">
                        <x-tables.rows.mobile.votes :model="$delegate" />
                    </span>
                </x-ark-tables.cell>
                <x-ark-tables.cell class="text-right" responsive breakpoint="md">
                    <x-tables.rows.desktop.productivity :model="$delegate" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
</x-ark-tables.table>
