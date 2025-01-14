@props(['wallet'])

<x-wallet.overview.item :title="trans('general.overview')">
    <x-wallet.overview.item-entry
        :title="trans('pages.wallet.name')"
        :value="$wallet->walletName()"
    />

    <x-wallet.overview.item-entry :title="trans('pages.wallet.balance')">
        <x-slot name="value">
            <span class="sm:hidden">
                <x-general.network-currency :value="$wallet->balance()" :decimals="2" />
            </span>
            <span class="hidden sm:inline">
                <x-general.network-currency :value="$wallet->balance()" />
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
