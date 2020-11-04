<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.filler />
                <x-tables.headers.desktop.text name="general.transaction.timestamp" />
                <x-tables.headers.desktop.address name="general.transaction.sender" />
                <x-tables.headers.desktop.address name="general.transaction.recipient" />
                <x-tables.headers.desktop.number name="general.transaction.amount" />
                <x-tables.headers.desktop.number name="general.transaction.fee" />
                @isset($useConfirmations)
                    <x-tables.headers.desktop.number name="general.transaction.confirmations" />
                @endisset
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td wire:key="{{ $transaction->id() }}-id">
                        <x-tables.rows.desktop.transaction-id :model="$transaction" />
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-tables.rows.desktop.timestamp :model="$transaction" />
                    </td>
                    <td wire:key="{{ $transaction->id() }}-sender">
                        @isset($useDirection)
                            <x-tables.rows.desktop.sender-with-direction :model="$transaction" :wallet="$wallet" />
                        @else
                            <x-tables.rows.desktop.sender :model="$transaction" />
                        @endif
                    </td>
                    <td wire:key="{{ $transaction->id() }}-recipient">
                        <x-tables.rows.desktop.recipient :model="$transaction" />
                    </td>
                    <td class="text-right">
                        @isset($useDirection)
                            @if($transaction->isSent($wallet->address()))
                                <x-tables.rows.desktop.amount-sent :model="$transaction" />
                            @else
                                <x-tables.rows.desktop.amount-received :model="$transaction" />
                            @endif
                        @else
                            <x-tables.rows.desktop.amount :model="$transaction" />
                        @endisset
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-tables.rows.desktop.fee :model="$transaction" />
                    </td>
                    @isset($useConfirmations)
                    <td class="hidden text-right xl:table-cell" wire:key="{{ $transaction->id() }}-confirmations">
                        <x-tables.rows.desktop.confirmations :model="$transaction" />
                    </td>
                    @endisset
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
