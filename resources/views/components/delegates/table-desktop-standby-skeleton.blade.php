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
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.rank />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.username />
                    </td>
                    <td class="hidden text-right lg:table-cell">
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
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
