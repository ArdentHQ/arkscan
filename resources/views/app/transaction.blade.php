@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <div>
            <x-transaction.page-section :title="trans('pages.transaction.transaction_details')">
                <x-transaction.section-detail.row
                    :title="trans('pages.transaction.header.timestamp')"
                    :value="$transaction->timestamp()"
                    :transaction="$transaction"
                />

                <x-transaction.section-detail.row
                    :title="trans('pages.transaction.header.block')"
                    :transaction="$transaction"
                >
                    <x-transaction.section-detail.block-height-link
                        :id="$transaction->blockId()"
                        :height="$transaction->blockHeight()"
                    />
                </x-transaction.section-detail.row>

                <x-transaction.section-detail.row
                    :title="trans('pages.transaction.header.nonce')"
                    :transaction="$transaction"
                >
                    <x-number>{{ $transaction->nonce() }}</x-number>
                </x-transaction.section-detail.row>
            </x-transaction.page-section>

            <x-transaction.section-detail.transaction-type :transaction="$transaction" />

            <x-transaction.section-detail.addressing-section :transaction="$transaction" />

            <x-transaction.section-detail.summary-section :transaction="$transaction" />

            @if ($transaction->isTransfer() || $transaction->isMultiPayment())
                <x-transaction.section-detail.memo-section :transaction="$transaction" />
            @endif

            <x-transaction.section-detail.confirmations-section :transaction="$transaction" />

            @if ($transaction->isMultiPayment())
                <x-transaction.section-detail.recipient-list-section :transaction="$transaction" />
            @elseif ($transaction->isMultisignature())
                <x-transaction.section-detail.participant-list-section :transaction="$transaction" />
            @endif
        </div>
    @endsection
@endcomponent
