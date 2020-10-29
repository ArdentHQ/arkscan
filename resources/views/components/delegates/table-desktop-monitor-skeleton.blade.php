<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th>@lang('pages.monitor.order')</th>
                <th><span class="pl-14">@lang('pages.monitor.name')</span></th>
                <th><span class="pl-14">@lang('pages.monitor.forging_at')</span></th>
                <th>@lang('pages.monitor.status')</th>
                <th width="120" class="hidden text-right lg:table-cell">@lang('pages.monitor.block_id')</th>
            </tr>
        </thead>
        <tbody>
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.slot-id />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.username-with-avatar />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.slot-time />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.round-status />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.skeleton.wallet-last-block />
                    </td>
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
