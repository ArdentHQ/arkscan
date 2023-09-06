<div class="divide-y table-list-mobile">
    @foreach ($blocks as $block)
        <div class="table-list-mobile-row" wire:key="{{ Helpers::generateId('mobile', $block->id(), Settings::currency()) }}">
            <x-tables.rows.mobile.block-id :model="$block" />

            <x-tables.rows.mobile.timestamp :model="$block" />

            @if(! isset($withoutGenerator))
                <x-tables.rows.mobile.block-forger :model="$block" />
            @endif

            <x-tables.rows.mobile.block-height :model="$block" />

            <x-tables.rows.mobile.transaction-count :model="$block" />

            <x-tables.rows.mobile.amount :model="$block" />

            <x-tables.rows.mobile.fee :model="$block" />
        </div>
    @endforeach
</div>
