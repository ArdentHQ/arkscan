<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.number name="pages.delegates.order" alignment="text-left" />
                <x-tables.headers.desktop.address name="pages.delegates.name" />
                <x-tables.headers.desktop.text name="pages.delegates.forging_at" alignment="text-left" />
                <x-tables.headers.desktop.status name="pages.delegates.status" />
                <x-tables.headers.desktop.text name="pages.delegates.block_id" />
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
                    <td wire:key="{{ $delegate->publicKey() }}-username-desktop">
                        <x-tables.rows.desktop.username-with-avatar :model="$delegate->wallet()" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.slot-time :model="$delegate" />
                    </td>
                    <td wire:key="{{ $delegate->publicKey() }}-round-status-{{ $delegate->status() }}-desktop">
                        <x-tables.rows.desktop.round-status :model="$delegate" />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.wallet-last-block :model="$delegate" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
