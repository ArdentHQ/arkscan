@use('ARKEcosystem\Foundation\NumberFormatter\NumberFormatter')

@props(['wallet'])

@php
    $rawBalance = $wallet->balance();
    $formattedBalanceTwoDecimals = NumberFormatter::new()->formatWithCurrencyCustom($rawBalance, Network::currency(), 2);
    $formattedBalanceFull = NumberFormatter::new()->formatWithCurrencyCustom($rawBalance, Network::currency(), null);
    $showTooltip = $formattedBalanceTwoDecimals !== $formattedBalanceFull;
@endphp

<x-wallet.overview.item :title="trans('general.overview')">
    <x-wallet.overview.item-entry
        :title="trans('pages.wallet.name')"
        :value="$wallet->walletName()"
    />

    <x-wallet.overview.item-entry :title="trans('pages.wallet.balance')">
       
        <x-slot name="value">
            <span class="sm:hidden" @if($showTooltip) data-tippy-content="{{ $formattedBalanceFull }}" @endif>
                <x-general.network-currency :value="$rawBalance" :decimals="2" />
            </span>
            <span class="hidden sm:inline">
                <x-general.network-currency :value="$rawBalance" />
            </span>
        </x-slot>
    </x-wallet.overview.item-entry>

    <x-wallet.overview.item-entry :title="trans('pages.wallet.value')">
        <x-slot name="value">
            @if (Network::canBeExchanged())
                <livewire:fiat-value :amount="$wallet->balance()" />
            @endif
        </x-slot>
    </x-wallet.overview.item-entry>

    <x-wallet.overview.item-entry :title="trans('pages.wallet.voting_for')">
        <x-slot name="value">
            @if ($wallet->vote())
                <x-general.identity-iconless :model="$wallet->vote()" />
            @endif
        </x-slot>
    </x-wallet.overview.item-entry>
</x-wallet.overview.item>