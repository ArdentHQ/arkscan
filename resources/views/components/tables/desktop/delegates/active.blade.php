<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.number name="general.delegates.rank" alignment="text-left" />
                <x-tables.headers.desktop.address name="general.delegates.name" />
                <x-tables.headers.desktop.status name="general.delegates.status" />
                <x-tables.headers.desktop.number name="general.delegates.votes" responsive breakpoint="lg"/>
                @if (Network::usesMarketSquare())
                    <x-tables.headers.desktop.icon name="general.delegates.profile" />
                    <x-tables.headers.desktop.number name="general.delegates.commission" responsive />
                @endif
                <x-tables.headers.desktop.number name="general.delegates.productivity" />
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr>
                    <td>
                        <x-tables.rows.desktop.rank :model="$delegate" />
                    </td>
                    <td wire:key="{{ $delegate->username() }}-username">
                        <x-tables.rows.desktop.username :model="$delegate" />
                    </td>
                    <td wire:key="{{ $delegate->username() }}-round-status-history">
                        <x-tables.rows.desktop.round-status-history :model="$delegate" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.votes :model="$delegate" />
                    </td>
                    @if (Network::usesMarketSquare())
                        <td>
                            <x-tables.rows.desktop.marketsquare-profile :model="$delegate" />
                        </td>
                        <td class="hidden xl:table-cell">
                            <x-tables.rows.desktop.marketsquare-commission :model="$delegate" />
                        </td>
                    @endif
                    <td class="text-right">
                        <x-tables.rows.desktop.productivity :model="$delegate" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
