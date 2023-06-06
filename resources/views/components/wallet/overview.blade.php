<div class="md:pb-6 md:mx-auto md:max-w-7xl md:px-8 lg:px-10">
    <div class="flex flex-col md:space-x-3 md:flex-row">
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

        @if ($wallet->isDelegate())
            <x-wallet.overview.item :title="trans('pages.wallet.delegate_info')">
                <x-wallet.overview.item-entry
                    :title="trans('pages.wallet.delegate.rank')"
                    :value="$wallet->rank()"
                    :has-empty-value="! $wallet->isDelegate()"
                >
                    <x-slot name="value">
                        <x-wallet.overview.delegate.rank :wallet="$wallet" />
                    </x-slot>
                </x-wallet.overview.item-entry>

                <x-wallet.overview.item-entry
                    :title="trans('pages.wallet.delegate.votes_title')"
                    :has-empty-value="! $wallet->isDelegate()"
                >
                    <x-slot name="value">
                        @if ($wallet->isDelegate())
                            <x-general.network-currency
                                :value="$wallet->votes()"
                                decimals="0"
                            />
                        @endif
                    </x-slot>

                    <x-slot name="tooltip">
                        @if ($wallet->votes())
                            <x-general.network-currency :value="$wallet->votes()" />
                        @endif
                    </x-slot>
                </x-wallet.overview.item-entry>

                <x-wallet.overview.item-entry
                    :title="trans('pages.wallet.delegate.productivity_title')"
                    :has-empty-value="! $wallet->isDelegate() || ! $wallet->isActive()"
                >
                    <x-slot name="value">
                        <x-wallet.overview.delegate.productivity :wallet="$wallet" />
                    </x-slot>
                </x-wallet.overview.item-entry>

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
    </div>
</div>
