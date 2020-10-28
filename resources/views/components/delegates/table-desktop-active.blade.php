<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th>@lang('general.delegates.rank')</th>
                <th><span class="pl-14">@lang('general.delegates.name')</span></th>
                <th><span class="pl-14">@lang('general.delegates.status')</span></th>
                <th>@lang('general.delegates.votes')</th>
                @if (Network::usesMarketSquare())
                <th>@lang('general.delegates.profile')</th>
                <th>@lang('general.delegates.commission')</th>
                @endif
                <th width="120" class="hidden text-right lg:table-cell">@lang('general.delegates.productivity')</th>
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
                    <td>
                        <x-tables.rows.desktop.round-status-history :model="$delegate" />
                    </td>
                    <td>
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
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.productivity :model="$delegate" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
