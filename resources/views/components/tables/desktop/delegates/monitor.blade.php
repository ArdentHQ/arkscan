<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th>@lang('pages.delegates.order')</th>
                <th><span class="pl-14">@lang('pages.delegates.name')</span></th>
                <th><span class="pl-14">@lang('pages.delegates.forging_at')</span></th>
                <th>@lang('pages.delegates.status')</th>
                <th width="120" class="hidden text-right lg:table-cell">@lang('pages.delegates.block_id')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr
                    wire:key="$delegate->publicKey()"
                    @if ($delegate->keepsMissing())
                        class="bg-theme-danger-50"
                    @elseif ($delegate->justMissed())
                        class="bg-theme-warning-50"
                    @endif
                >
                    <td>
                        <x-tables.rows.desktop.slot-id :model="$delegate" />
                    </td>
                    <td wire:key="{{ $delegate->publicKey() }}-username">
                        <x-tables.rows.desktop.username-with-avatar :model="$delegate->wallet()" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.slot-time :model="$delegate" />
                    </td>
                    <td wire:key="{{ $delegate->publicKey() }}-round-status-{{ $delegate->status() }}">
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
