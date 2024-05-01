@props(['wallet'])

@if ($wallet->isDelegate())
    <x-wallet.overview.item :title="trans('pages.wallet.delegate_info')">
        @if (! $wallet->isResigned())
            <x-slot name="titleExtra">
                <div x-cloak>
                    <x-delegates.vote-link
                        :model="$wallet"
                        button-class="font-semibold hover:underline"
                    >
                        <x-slot name="voteText">
                            <span class="md:hidden">
                                @lang('actions.vote')
                            </span>

                            <span class="hidden md:inline">
                                @lang('pages.wallet.delegate.vote_for_delegate')
                            </span>
                        </x-slot>

                        <x-slot name="unvoteText">
                            <span class="md:hidden">
                                @lang('actions.unvote')
                            </span>

                            <span class="hidden md:inline">
                                @lang('pages.wallet.delegate.unvote_delegate')
                            </span>
                        </x-slot>
                    </x-delegates.vote-link>
                </div>
            </x-slot>
        @endif

        <x-wallet.overview.delegate.rank :wallet="$wallet" />

        <x-wallet.overview.delegate.votes :wallet="$wallet" />

        <x-wallet.overview.delegate.productivity :wallet="$wallet" />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.delegate.forged_total')"
            :has-empty-value="! $wallet->isDelegate()"
        >
            <x-slot name="value">
                @if ($wallet->isDelegate())
                    <x-general.network-currency
                        :value="$wallet->totalForged()"
                        :decimals="0"
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
        class="hidden md-lg:block"
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
