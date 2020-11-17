<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.number name="general.delegates.rank" alignment="text-left" />
                <x-tables.headers.desktop.address name="general.delegates.name" />
                <x-tables.headers.desktop.status name="general.delegates.status">
                    <x-ark-info :tooltip="trans('pages.delegates.info.status')" />
                </x-tables.headers.desktop.status>
                <x-tables.headers.desktop.number name="general.delegates.votes" responsive breakpoint="lg"/>
                @if (Network::usesMarketSquare())
                    <x-tables.headers.desktop.icon name="general.delegates.profile" />
                    <x-tables.headers.desktop.number name="general.delegates.commission" responsive />
                @endif
                <x-tables.headers.desktop.number name="general.delegates.productivity">
                    <x-ark-info :tooltip="trans('pages.delegates.info.productivity')" />
                </x-tables.headers.desktop.number>
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <x-ark-tables.row>
                    <x-ark-tables.cell>
                        <x-tables.rows.desktop.rank :model="$delegate" />
                    </x-ark-tables.cell>
                    <x-ark-tables.cell wire:key="{{ $delegate->username() }}-username">
                        <x-tables.rows.desktop.username :model="$delegate" />
                    </x-ark-tables.cell>
                    <x-ark-tables.cell wire:key="{{ $delegate->username() }}-round-status-history">
                        <x-tables.rows.desktop.round-status-history :model="$delegate" />
                    </x-ark-tables.cell>
                    <x-ark-tables.cell class="text-right" responsive>
                        <x-tables.rows.desktop.votes :model="$delegate" />
                    </x-ark-tables.cell>
                    @if (Network::usesMarketSquare())
                        <x-ark-tables.cell>
                            <x-tables.rows.desktop.marketsquare-profile :model="$delegate" />
                        </x-ark-tables.cell>
                        <x-ark-tables.cell responsive breakpoint="xl">
                            <x-tables.rows.desktop.marketsquare-commission :model="$delegate" />
                        </x-ark-tables.cell>
                    @endif
                    <x-ark-tables.cell class="text-right">
                        <x-tables.rows.desktop.productivity :model="$delegate" />
                    </x-ark-tables.cell>
                </x-ark-tables.row>
            @endforeach
        </tbody>
    </table>
</div>
