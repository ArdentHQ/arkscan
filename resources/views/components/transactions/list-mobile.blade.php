<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="space-x-10 table-list-mobile-row sm:space-x-8">
            <div class="space-y-6">
                <div>@lang('general.transaction.id')</div>
                <div>@lang('general.transaction.timestamp')</div>
                <div>@lang('general.transaction.sender')</div>
                <div>@lang('general.transaction.recipient')</div>
                <div>@lang('general.transaction.amount')</div>
                <div>@lang('general.transaction.fee')</div>
            </div>

            <div class="flex-1 space-y-6">
                <div><a href="{{ $transaction->url() }}" class="font-semibold link">{{ $transaction->id() }}</a></div>
                <div>{{ $transaction->timestamp() }}</div>
                <div><x-general.address address="{{ $transaction->sender() }}" /></div>
                <div><x-general.address address="{{ $transaction->recipient() ?? $transaction->sender() }}" /></div>
                <div>{{ $transaction->amount() }}</div>
                <div>{{ $transaction->fee() }}</div>
            </div>
        </div>
    @endforeach
</div>
