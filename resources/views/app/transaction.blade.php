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
                    :value="$transaction->nonce()"
                    :transaction="$transaction"
                />
            </x-transaction.page-section>

            <x-transaction.section-detail.transaction-type :transaction="$transaction" />
        </div>
    @endsection
@endcomponent
