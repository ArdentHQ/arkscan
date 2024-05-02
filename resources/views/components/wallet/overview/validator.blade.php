@props(['wallet'])

@if ($wallet->isValidator())
    <x-wallet.overview.item :title="trans('pages.wallet.validator_info')">
        @if (! $wallet->isResigned() && config('arkscan.arkconnect.enabled'))
            <x-slot name="titleExtra">
                <div x-cloak>
                    <x-validators.vote-link
                        :model="$wallet"
                        button-class="font-semibold hover:underline"
                    >
                        <x-slot name="voteText">
                            <span class="md:hidden">
                                @lang('actions.vote')
                            </span>

                            <span class="hidden md:inline">
                                @lang('pages.wallet.validator.vote_for_validator')
                            </span>
                        </x-slot>

                        <x-slot name="unvoteText">
                            <span class="md:hidden">
                                @lang('actions.unvote')
                            </span>

                            <span class="hidden md:inline">
                                @lang('pages.wallet.validator.unvote_validator')
                            </span>
                        </x-slot>
                    </x-validators.vote-link>
                </div>
            </x-slot>
        @endif

        <x-wallet.overview.validator.rank :wallet="$wallet" />

        <x-wallet.overview.validator.votes :wallet="$wallet" />

        <x-wallet.overview.validator.productivity :wallet="$wallet" />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.validator.forged_total')"
            :has-empty-value="! $wallet->isValidator()"
        >
            <x-slot name="value">
                @if ($wallet->isValidator())
                    <x-general.network-currency
                        :value="$wallet->totalForged()"
                        :decimals="0"
                    />
                @endif
            </x-slot>

            <x-slot name="tooltip">
                @if ($wallet->isValidator())
                    <x-general.network-currency :value="$wallet->totalForged()" />
                @endif
            </x-slot>
        </x-wallet.overview.item-entry>
    </x-wallet.overview.item>
@else
    <x-wallet.overview.item
        :title="trans('pages.wallet.validator_info')"
        :masked-message="trans('pages.wallet.validator.not_registered_text')"
        class="hidden md-lg:block"
    >
        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.validator.rank')"
            :value="$wallet->rank()"
        />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.validator.votes_title')"
            :value="$wallet->rank()"
        />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.validator.productivity_title')"
            :value="$wallet->rank()"
        />

        <x-wallet.overview.item-entry
            :title="trans('pages.wallet.validator.forged_total')"
            :value="$wallet->rank()"
        />
    </x-wallet.overview.item>
@endif
