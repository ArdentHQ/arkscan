<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="space-y-3 table-list-mobile-row">
            <x-tables.rows.mobile.transaction-id :model="$transaction" />

            <x-tables.rows.mobile.timestamp :model="$transaction" />

            <x-tables.rows.mobile.sender :model="$transaction" />

            <x-tables.rows.mobile.recipient :model="$transaction" />

            <x-tables.rows.mobile.amount :model="$transaction" />

            <x-tables.rows.mobile.fee :model="$transaction" />
        </div>
    @endforeach
</div>
