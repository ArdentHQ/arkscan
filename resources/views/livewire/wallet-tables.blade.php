@php
    $items = [
        'transactions' => trans('pages.wallet.transactions'),
    ];

    if ($wallet->isDelegate()) {
        $items['blocks'] = trans('pages.wallet.delegate.validated_blocks');
        $items['voters'] = trans('pages.wallet.delegate.voters');
    }
@endphp

<div
    x-data="{ tab: @entangle('view') }"
    wire:init="triggerViewIsReady"
>
    <x-tabs
        :default="$this->view"
        :options="$items"
    />

    <div id="wallet-table-list">
        <x-wallet.tables.transactions :wallet="$wallet" />

        @if($wallet->isDelegate())
            <x-wallet.tables.voters :wallet="$wallet" x-cloak />

            <x-wallet.tables.blocks :wallet="$wallet" x-cloak />

            <x-webhooks.reload-transactions :wallet="$wallet" />
            <x-webhooks.reload-blocks :public-key="$wallet->publicKey()" />
            <x-webhooks.reload-voters :public-key="$wallet->publicKey()" />
        @endif

        <x-script.onload-scroll-to-query selector="#wallet-table-list" />
    </div>
</div>

@push('scripts')
    @vite('resources/js/webhooks.js')
@endpush
