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
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.rank />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.username />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.round-status-history />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.votes />
                    </td>
                    @if (Network::usesMarketSquare())
                    <td>
                        <x-tables.rows.desktop.skeleton.marketsquare-profile />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.marketsquare-commission />
                    </td>
                    @endif
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.skeleton.productivity />
                    </td>
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
