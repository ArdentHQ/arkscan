@component('layouts.app')
    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <div @class([
            'mb-8' => ! $transaction->hasPayload(),
        ])>
            <x-transaction.page.details :transaction="$transaction" />

            <x-transaction.page.transaction-type :transaction="$transaction" />

            <x-transaction.page.addressing :transaction="$transaction" />

            <x-transaction.page.summary :transaction="$transaction" />

            <x-transaction.page.status :model="$transaction" />
        </div>

        @if ($transaction->hasPayload())
            <div class="mb-8">
                <x-transaction.page.more-details :transaction="$transaction" />
            </div>
        @endif
    @endsection
@endcomponent
