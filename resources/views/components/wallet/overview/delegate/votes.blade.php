@props(['wallet'])

<x-wallet.overview.item-entry
    :title="trans('pages.wallet.delegate.votes_title')"
    :has-empty-value="! $wallet->isDelegate()"
>
    <x-slot name="value">
        @if ($wallet->isDelegate())
            <span data-tippy-content="<x-general.network-currency :value="$wallet->votes()" />">
                <x-general.network-currency
                    :value="$wallet->votes()"
                    decimals="0"
                />
            </span>

            <a
                href="{{ route('wallet.voters', $wallet->model()) }}"
                class="link"
            >
                @lang('general.view')
            </a>
        @endif
    </x-slot>
</x-wallet.overview.item-entry>
