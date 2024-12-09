@component('layouts.app')
    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

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
        </div>

        <div class="mb-8">
            <x-transaction.page.more-details :transaction="$transaction" />
        </div>
    @endsection
@endcomponent
