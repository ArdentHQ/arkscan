<div class="space-y-8 divide-y md:hidden">
    @foreach ($transactions as $transaction)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.transaction-id :model="$transaction" />

            <x-tables.rows.mobile.timestamp :model="$transaction" />

            <x-tables.rows.mobile.sender :model="$transaction" />

            <x-tables.rows.mobile.recipient :model="$transaction" />

            <x-tables.rows.mobile.amount :model="$transaction" />

            <x-tables.rows.mobile.fee :model="$transaction" />
        </div>
    @endforeach
</div>
