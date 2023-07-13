@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <div>
            <x-transaction.page-section
                :title="trans('pages.transaction.transaction_details')"
                :items="[
                    trans('pages.transaction.header.timestamp') => $transaction->timestamp(),
                    trans('pages.transaction.header.block')     => [
                        'component' => '<x-transaction.section-detail.block-height-link
                            :id=\'$id\'
                            :height=\'$height\'
                        />',

                        'data' => [
                            'id'     => $transaction->blockId(),
                            'height' => $transaction->blockHeight(),
                        ],
                    ],
                    trans('pages.transaction.header.nonce')     => $transaction->nonce(),
                ]"
            />

            <x-transaction.page-section
                :title="trans('pages.transaction.transaction_type')"
                :items="[
                    trans('pages.transaction.header.category') => [
                        'component' => '<x-general.encapsulated.transaction-type-badge
                            :transaction=\'$transaction\'
                            class=\'inline-block\'
                        />',

                        'data' => [
                            'transaction' => $transaction,
                        ],
                    ],
                ]"
            />
        </div>
    @endsection
@endcomponent
