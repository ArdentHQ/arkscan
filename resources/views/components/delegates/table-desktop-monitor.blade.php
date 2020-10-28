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
            @foreach($delegates as $delegate)
            @if ($delegate->isWarning())
                <tr class="bg-theme-warning-100">
            @elseif ($delegate->isDanger())
                <tr class="bg-theme-danger-100">
            @else
                <tr>
            @endif
                    <td>
                        <x-tables.rows.desktop.slot-id :model="$delegate" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.username-with-avatar :model="$delegate->wallet()" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.slot-time :model="$delegate" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.round-status :model="$delegate" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.wallet-last-block :model="$delegate" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
