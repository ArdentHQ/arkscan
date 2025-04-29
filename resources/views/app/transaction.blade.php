@component('layouts.app')
    <x-metadata page="transaction" :detail="['txid' => $transaction->hash()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <div>
            <x-transaction.page.details :transaction="$transaction" />

            <x-transaction.page.action :transaction="$transaction" />

            <x-transaction.page.addressing :transaction="$transaction" />

            @if ($transaction->isTokenTransfer())
                <x-transaction.page.token :transaction="$transaction" />
            @endif

            <x-transaction.page.summary :transaction="$transaction" />

            <x-transaction.page.status :model="$transaction" />
            
            @if ($transaction->isMultiPayment())
                <x-transaction.page.recipients :model="$transaction" />
            @endif
        </div>

        <div class="mb-8">
            <x-transaction.page.more-details :transaction="$transaction" />
        </div>
    @endsection
@endcomponent
