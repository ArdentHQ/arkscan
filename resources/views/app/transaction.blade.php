@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <div>
            <x-transaction.page.details :transaction="$transaction" />

            <x-transaction.page.transaction-type :transaction="$transaction" />

            <x-transaction.page.addressing :transaction="$transaction" />

            <x-transaction.page.summary :transaction="$transaction" />

            @if ($transaction->isTransfer() || $transaction->isMultiPayment())
                <x-transaction.page.memo :transaction="$transaction" />
            @endif

            <x-general.page-section.confirmations :model="$transaction" />

            @if ($transaction->isMultiPayment())
                <x-transaction.page.recipient-list :transaction="$transaction" />
            @elseif ($transaction->isMultisignature())
                <x-transaction.page.participant-list :transaction="$transaction" />
            @endif
        </div>
    @endsection
@endcomponent
