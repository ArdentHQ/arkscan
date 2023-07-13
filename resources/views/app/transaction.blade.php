@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" />

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        @php
            $width = 'w-[87px]';
            if ($transaction->isVoteCombination()) {
                $width = 'w-[109px]';
            } elseif ($transaction->isLegacy()) {
                $width = 'w-[110px]';
            }
        @endphp

        <div>
            <x-transaction.page-section
                :width="$width"
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

            <x-transaction.section-detail.transaction-type
                :width="$width"
                :transaction="$transaction"
            />
        </div>
    @endsection
@endcomponent
