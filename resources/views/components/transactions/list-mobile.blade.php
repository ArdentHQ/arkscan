<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.transaction.id')</td>
                    <td>
                        <a href="{{ $transaction->url() }}" class="font-semibold link">{{ $transaction->id() }}</a>
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
                    <td>{{ $transaction->amount() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.fee')</td>
                    <td>{{ $transaction->fee() }}</td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
