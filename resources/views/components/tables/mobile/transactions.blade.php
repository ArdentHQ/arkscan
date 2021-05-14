<div class="divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="space-y-3 table-list-mobile-row">
            <x-tables.rows.mobile.transaction-id :model="$transaction" />

            <x-tables.rows.mobile.timestamp :model="$transaction" />

            <x-tables.rows.mobile.sender :model="$transaction" />

            <x-tables.rows.mobile.recipient :model="$transaction" />

            @isset($useDirection)
                @if($transaction->isSent($wallet->address()))
                    <x-tables.rows.mobile.amount-sent :model="$transaction" />
                @else
                    <x-tables.rows.mobile.amount-received :model="$transaction" />
                @endif
            @else
                <x-tables.rows.mobile.amount :model="$transaction" />
            @endisset

            <x-tables.rows.mobile.fee :model="$transaction" />

            <x-tables.rows.mobile.confirmations :model="$transaction" />
        </div>
    @endforeach
</div>
