@props(['wallet'])

@if ($wallet->isDelegate())
    <x-wallet.overview.item :title="trans('pages.wallet.delegate_info')">
        @if (! $wallet->isResigned())
            <x-slot name="titleExtra">
                <x-ark-external-link :url="$wallet->voteUrl()">
                    <x-slot name="text">
                        <span class="md:hidden">
                            @lang('pages.wallet.delegate.vote')
                        </span>

                        <span class="hidden md:inline">
                            @lang('pages.wallet.delegate.vote_for_delegate')
                        </span>
                    </x-slot>
                </x-ark-external-link>
            </x-slot>
        @endif

        <x-wallet.overview.delegate.rank :wallet="$wallet" />

        <x-wallet.overview.delegate.votes :wallet="$wallet" />

        @unless ($wallet->isResigned())
            <x-wallet.overview.delegate.productivity :wallet="$wallet" />
        @endunless

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.delegate.forged_total')"
            :has-empty-value="! $wallet->isDelegate()"
        >
            <x-slot name="value">
                @if ($wallet->isDelegate())
                    <x-general.network-currency
                        :value="$wallet->totalForged()"
                        decimals="0"
                    />
                @endif
            </x-slot>

            <x-slot name="tooltip">
                @if ($wallet->isDelegate())
                    <x-general.network-currency :value="$wallet->totalForged()" />
                @endif
            </x-slot>
        </x-wallet.overview.item-entry>
    </x-wallet.overview.item>
@else
    <x-wallet.overview.item
        :title="trans('pages.wallet.delegate_info')"
        :masked-message="trans('pages.wallet.delegate.not_registered_text')"
    >
        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.delegate.rank')"
            :value="$wallet->rank()"
        />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.delegate.votes_title')"
            :value="$wallet->rank()"
        />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.delegate.productivity_title')"
            :value="$wallet->rank()"
        />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.delegate.forged_total')"
            :value="$wallet->rank()"
        />
    </x-wallet.overview.item>
@endif
