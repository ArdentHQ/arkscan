@php
    $items = [
        'transactions' => trans('pages.wallet.transactions'),
    ];

    if ($wallet->isValidator()) {
        $items['blocks'] = trans('pages.wallet.validator.validated_blocks');
        $items['voters'] = trans('pages.wallet.validator.voters');
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

        @if($wallet->isValidator())
            <x-wallet.tables.voters :wallet="$wallet" x-cloak />
            <x-wallet.tables.blocks :wallet="$wallet" x-cloak />
        @endif

        <x-script.onload-scroll-to-query selector="#wallet-table-list" />
    </div>
</div>

@push('scripts')
    @vite('resources/js/webhooks.js')
@endpush
