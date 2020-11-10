<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.number name="general.delegates.rank" alignment="text-left" />
                <x-tables.headers.desktop.address name="general.delegates.name" />
                <x-tables.headers.desktop.number name="general.delegates.votes" responsive breakpoint="lg" />
                @if (Network::usesMarketSquare())
                    <x-tables.headers.desktop.icon name="general.delegates.profile" />
                    <x-tables.headers.desktop.number name="general.delegates.commission" responsive />
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr>
                    <td>
                        <x-tables.rows.desktop.rank :model="$delegate" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.username :model="$delegate" />
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
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
