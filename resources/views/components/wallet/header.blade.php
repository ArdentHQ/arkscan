<x-wallet.header-frame title="pages.wallet.title" :wallet="$wallet">
    <x-wallet.header-frame-item icon="wallet" title="pages.wallet.balance">
        <x-currency>{{ $wallet->balance() }}</x-currency>
    </x-wallet.header-frame-item>

    <x-slot name="extra">
        @if($wallet->isVoting())
            @php($vote = $wallet->vote())

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.wallet.voting_for')"
                        :avatar="$vote->username()"
                        :text="$vote->username()"
                        :url="route('wallet', $vote->address())"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.wallet.rank')"
                        icon="app-votes"
                    >
                        <x-slot name="text">
                            @lang('pages.wallet.vote_rank', [$vote->rank()])
                        </x-slot>
                    </x-general.entity-header-item>
                    @if (Network::usesMarketSquare())
                        <x-general.entity-header-item
                            :title="trans('pages.wallet.commission')"
                            icon="exchange"
                            :text="$vote->commission()"
                        />
                    @endif
                </div>
            </x-slot>
        @endif
    </x-slot>
</x-wallet.header-frame>
