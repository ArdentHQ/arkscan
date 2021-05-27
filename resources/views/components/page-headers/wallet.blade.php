<x-page-headers.wallet.frame title="pages.wallet.title" :wallet="$wallet">
    <x-page-headers.wallet.frame-item icon="wallet" title-class="whitespace-nowrap">
        <x-slot name="title">
            @lang('pages.wallet.balance')@if(Network::canBeExchanged()): <livewire:wallet-balance :wallet="$wallet->model()" /> @endif
        </x-slot>

        <x-currency :currency="Network::currency()">{{ $wallet->balance() }}</x-currency>
    </x-page-headers.wallet.frame-item>

    @if($wallet->isVoting())
        <x-slot name="extension">
            @php($vote = $wallet->vote())

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                <x-general.entity-header-item
                    :title="trans('pages.wallet.voting_for')"
                    :avatar="$vote->address()"
                    :text="$vote->username()"
                    :url="route('wallet', $vote->address())"
                />
                <x-general.entity-header-item
                    :title="trans('pages.wallet.rank')"
                    icon="app-votes"
                >
                    <x-slot name="text">
                        @if ($wallet->isResigned())
                            <x-details.resigned />
                        @else
                            @lang('pages.wallet.vote_rank', [$vote->rank()])
                        @endif
                    </x-slot>
                </x-general.entity-header-item>
            </div>
        </x-slot>
    @endif
</x-page-headers.wallet.frame>
