<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">
                        <div>@lang('general.transaction.id')</div>
                    </td>
                    <td>
                        <div><a href="{{ $transaction->url() }}" class="font-semibold link">{{ $transaction->id() }}</a></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.transaction.timestamp')</div>
                    </td>
                    <td>
                        <div>{{ $transaction->timestamp() }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.transaction.sender')</div>
                    </td>
                    <td>
                        <div><x-general.address :address="$transaction->sender()" /></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.transaction.recipient')</div>
                    </td>
                    <td>
                        <div><x-general.address :address="$transaction->recipient() ?? $transaction->sender()" /></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.transaction.amount')</div>
                    </td>
                    <td>
                        <div>{{ $transaction->amount() }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.transaction.fee')</div>
                    </td>
                    <td>
                        <div>{{ $transaction->fee() }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
