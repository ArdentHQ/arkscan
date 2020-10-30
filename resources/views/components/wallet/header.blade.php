<div class="dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 mb-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.wallet.title')" />

        <x-general.entity-header :value="$wallet->address()">
            <x-slot name="title">
                @if($wallet->isDelegate())
                    @lang('pages.wallet.address_delegate', [$wallet->username()])
                @else
                    @lang('pages.wallet.address')
                @endif
            </x-slot>

            <x-slot name="logo">
                @if($wallet->isDelegate())
                    <x-headings.avatar-with-icon :model="$wallet" icon="app-delegate" />
                @else
                    <x-headings.avatar :model="$wallet" />
                @endif
            </x-slot>

            <x-slot name="extra">
                <div class="flex flex-col justify-between flex-1 pl-4 font-semibold border-l md:ml-8 border-theme-secondary-800">
                    <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">
                        @lang('pages.wallet.balance')
                    </div>

                    <div class="flex items-center space-x-2 leading-tight">
                        <span class="truncate text-theme-secondary-400 dark:text-theme-secondary-200">
                            <x-currency>{{ $wallet->balance() }}</x-currency>
                        </span>
                    </div>
                </div>

                <div class="flex items-center mt-6 space-x-2 text-theme-secondary-200 md:mt-0">
                    {{-- @TODO: public key button --}}
                    <a href="#" class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-primary-600 transition-default md:flex-none">
                        @svg('app-key', 'w-6 h-6')
                    </a>

                    <button @click="livewire.emit('toggleQrCode')" type="button" class="flex items-center justify-center flex-1 h-full px-3 rounded cursor-pointer bg-theme-secondary-800 hover:bg-theme-primary-600 transition-default md:flex-none">
                        @svg('app-qr-code', 'w-6 h-6')
                    </button>
                </div>
            </x-slot>

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
        </x-general.entity-header>
    </div>
</div>
