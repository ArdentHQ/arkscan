@props(['wallet'])

@if ($wallet->isValidator())
    <x-wallet.overview.item :title="trans('pages.wallet.validator_info')">
        @if (! $wallet->isResigned())
            <x-slot name="titleExtra">
                <x-ark-external-link
                    :url="$wallet->voteUrl()"
                    icon-class="inline relative -top-1 flex-shrink-0 mt-1 ml-0.5 text-theme-secondary-500 dark:text-theme-dark-500"
                >
                    <x-slot name="text">
                        <span class="md:hidden">
                            @lang('pages.wallet.validator.vote')
                        </span>

                        <span class="hidden md:inline">
                            @lang('pages.wallet.validator.vote_for_validator')
                        </span>
                    </x-slot>
                </x-ark-external-link>
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
