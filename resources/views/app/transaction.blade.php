@component('layouts.app')
    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <div>
            <x-transaction.page.details :transaction="$transaction" />

            <x-transaction.page.transaction-type :transaction="$transaction" />

            <x-transaction.page.addressing :transaction="$transaction" />

            <x-transaction.page.summary :transaction="$transaction" />

            <x-general.page-section.confirmations :model="$transaction" />
        </div>

        <div class="mb-8">
            <x-transaction.page.more-details :transaction="$transaction" />
        </div>
    @endsection
@endcomponent
