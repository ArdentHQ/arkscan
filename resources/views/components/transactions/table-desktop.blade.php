<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center"></th>
                <th class="hidden lg:table-cell">@lang('general.transaction.timestamp')</th>
                <th><span class="pl-24">@lang('general.transaction.sender')</span></th>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th class="text-right">@lang('general.transaction.amount')</th>
                <th class="hidden text-right xl:table-cell">@lang('general.transaction.fee')</th>
                @isset($useConfirmations)
                    <th class="hidden text-right xl:table-cell">@lang('general.transaction.confirmations')</th>
                @endisset
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>
                        <x-tables.rows.desktop.transaction-id :model="$transaction" />
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-tables.rows.desktop.timestamp :model="$transaction" />
                    </td>
                    <td>
                        @isset($useDirection)
                            <x-tables.rows.desktop.sender-with-direction :model="$transaction" :wallet="$wallet" />
                        @else
                            <x-tables.rows.desktop.sender :model="$transaction" />
                        @endif
                    </td>
                    <td>
                        <x-tables.rows.desktop.recipient :model="$transaction" />
                    </td>
                    <td class="text-right">
                        {{-- @TODO: this could potentially be abstracted into an amount component that handles this --}}
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
                        <x-tables.rows.desktop.confirmations :model="$transaction" />
                    @endisset
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
