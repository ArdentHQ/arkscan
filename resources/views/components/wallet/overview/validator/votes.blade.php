@props(['wallet'])

<x-wallet.overview.item-entry
    :title="trans('pages.wallet.validator.votes_title')"
    :has-empty-value="! $wallet->isValidator()"
>
    <x-slot name="value">
        @if ($wallet->isValidator())
            <span data-tippy-content="<x-general.network-currency :value="$wallet->votes()" />">
                <x-general.network-currency
                    :value="$wallet->votes()"
                    :decimals="0"
                />
            </span>

            <button
                x-data="{}"
                type="button"
                x-on:click="() => {
                    Livewire.dispatch('showWalletView', {view: 'voters'});
                    scrollToQuery('#wallet-table-list');
                }"
                class="link"
            >
                @lang('general.view')
            </a>
        @endif
    </x-slot>
</x-wallet.overview.item-entry>
