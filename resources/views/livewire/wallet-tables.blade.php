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
    x-data="{ tab: @entangle('view').live }"
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
        @endif

        <x-script.onload-scroll-to-query selector="#wallet-table-list" />
    </div>
</div>

@push('scripts')
    @vite('resources/js/webhooks.js')
@endpush
