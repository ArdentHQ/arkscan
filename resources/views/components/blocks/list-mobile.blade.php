<div class="space-y-8 divide-y md:hidden">
    @foreach ($blocks as $block)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.block-id :model="$block" />

            <x-tables.rows.mobile.timestamp :model="$block" />

            <x-tables.rows.mobile.block-forger :model="$block" />

            <x-tables.rows.mobile.block-height :model="$block" />

            <x-tables.rows.mobile.transaction-count :model="$block" />

            <x-tables.rows.mobile.amount :model="$block" />

            <x-tables.rows.mobile.fee :model="$block" />
        </div>
    @endforeach
</div>
