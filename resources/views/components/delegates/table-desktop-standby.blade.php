<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th width="60">@lang('general.delegates.rank')</th>
                <th><span class="pl-14">@lang('general.delegates.name')</span></th>
                <th width="250" class="hidden text-right lg:table-cell">@lang('general.delegates.votes')</th>
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
                    <td>
                        <x-tables.rows.desktop.marketsquare-commission :model="$delegate" />
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
