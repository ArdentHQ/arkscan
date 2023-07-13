@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <x-transaction.page-section
            class="sm:-mt-2"
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
    @endsection
@endcomponent
