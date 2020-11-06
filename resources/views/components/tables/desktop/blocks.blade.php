<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.text name="general.block.id" />
                <x-tables.headers.desktop.text name="general.block.timestamp" responsive />
                @if(!isset($withoutGenerator))
                    <x-tables.headers.desktop.address name="general.block.generated_by" />
                @endif
                <x-tables.headers.desktop.number name="general.block.height" />
                <x-tables.headers.desktop.number name="general.block.transactions" />
                <x-tables.headers.desktop.number name="general.block.amount" />
                <x-tables.headers.desktop.number name="general.block.fee" responsive />
            </tr>
        </thead>
        <tbody>
            @foreach($blocks as $block)
                <tr>
                    <td wire:key="{{ $block->id() }}-id">
                        <x-tables.rows.desktop.block-id :model="$block" />
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-tables.rows.desktop.timestamp :model="$block" />
                    </td>
                    @if(!isset($withoutGenerator))
                        <td wire:key="{{ $block->id() }}-forger">
                            <x-tables.rows.desktop.block-forger :model="$block" />
                        </td>
                    @endif
                    <td class="text-right">
                        <x-tables.rows.desktop.block-height :model="$block" />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.transaction-count :model="$block" />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.amount :model="$block" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.fee :model="$block" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
