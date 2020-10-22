<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.transaction.id')</td>
                    <td>
                        <a href="{{ $transaction->url() }}" class="font-semibold link">
                            <x-truncate-middle :value="$transaction->id()" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.timestamp')</td>
                    <td>{{ $transaction->timestamp() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.sender')</td>
                    <td><x-general.address :address="$transaction->sender()" /></td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.recipient')</td>
                    <td><x-general.address :address="$transaction->recipient() ?? $transaction->sender()" /></td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.amount')</td>
                    <td>
                        <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.fee')</td>
                    <td>
                        <x-general.amount-fiat-tooltip :amount="$transaction->fee()" :fiat="$transaction->feeFiat()" />
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
