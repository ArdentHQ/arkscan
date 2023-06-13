@props(['wallet'])

<x-wallet.overview.item :title="trans('general.overview')">
    <x-wallet.overview.item-entry
        :title="trans('pages.wallet.name')"
        :value="$wallet->name()"
    />

    <x-wallet.overview.item-entry :title="trans('pages.wallet.balance')">
        <x-slot name="value">
            <x-general.network-currency :value="$wallet->balance()" />
        </x-slot>
    </x-wallet.overview.item-entry>

    <x-wallet.overview.item-entry :title="trans('pages.wallet.value')">
        <x-slot name="value">
            <span>{{ $wallet->balanceFiat() }}</span>

            <span>{{ Settings::currency() }}</span>
        </x-slot>
    </x-wallet.overview.item-entry>

    <x-wallet.overview.item-entry
        :title="trans('pages.wallet.voting_for')"
        :has-empty-value="$wallet->vote() === null"
    >
        <x-slot name="value">
            @if ($wallet->vote())
                <x-general.identity-iconless :model="$wallet->vote()" />
            @endif
        </x-slot>
    </x-wallet.overview.item-entry>
</x-wallet.overview.item>
