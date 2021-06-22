@props([
    'transactions',
    'wallet' => null,
    'useDirection' => false,
    'excludeItself' => false,
    'useConfirmations' => false,
    'isSent' => null,
    'isReceived' => null,
    'state' => [],
])

<div class="divide-y table-list-mobile" wire:key="{{ Helpers::generateId('transactions-mobile', ...$state) }}">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <x-tables.rows.mobile.transaction-id :model="$transaction" />

            <x-tables.rows.mobile.timestamp :model="$transaction" />

            <x-tables.rows.mobile.sender :model="$transaction" />

            <x-tables.rows.mobile.recipient :model="$transaction" />

            @if($useDirection)
                @if(($transaction->isSent($wallet->address()) || $isSent === true) && $isReceived !== true)
                    <x-tables.rows.mobile.amount-sent :model="$transaction" :exclude-itself="$excludeItself" />
                @else
                    <x-tables.rows.mobile.amount-received
                        :model="$transaction"
                        :wallet="$wallet"
                    />
                @endif
            @else
                <x-tables.rows.mobile.amount :model="$transaction" />
            @endif

            <x-tables.rows.mobile.fee :model="$transaction" />

            @if($useConfirmations)
                <x-tables.rows.mobile.confirmations :model="$transaction" />
            @endif
        </div>
    @endforeach
</div>
