<div class="space-y-8 divide-y md:hidden">
    @foreach ($wallets as $wallet)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <div class="flex justify-between w-full">
                @lang('general.wallet.address')

                <x-general.address :address="$wallet->address()" with-loading />
            </div>

            @if ($wallet->isKnown() || $wallet->isOwnedByExchange())
            <div class="flex justify-between w-full">
                @lang('general.wallet.info')

                <div class="flex flex-col space-y-4">
                    @if ($wallet->isKnown())
                    <div>
                        <div class="flex items-center space-x-4 ">
                            <x-general.loading-state.icon icon="app-verified" />
                            <x-general.loading-state.text :text="trans('general.verified_address')" />
                        </div>

                        <div class="flex items-center space-x-4" wire:loading.class="hidden">
                            @svg('app-verified', 'w-5 h-5 text-theme-secondary-500')

                            <span>@lang('general.verified_address')</span>
                        </div>
                    </div>
                    @endif

                    @if ($wallet->isOwnedByExchange())
                    <div>
                        <div class="flex items-center space-x-4 ">
                            <x-general.loading-state.icon icon="app-exchange" />
                            <x-general.loading-state.text :text="trans('general.exchange')" />
                        </div>

                        <div class="flex items-center space-x-4" wire:loading.class="hidden">
                            @svg('app-exchange', 'w-5 h-5 text-theme-secondary-500')

                            <span>@lang('general.exchange')</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="flex justify-between w-full">
                @lang('general.wallet.balance')

                <x-general.loading-state.text :text="$wallet->balance()" />

                <div wire:loading.class="hidden">
                    <x-general.amount-fiat-tooltip :amount="$wallet->balance()" :fiat="$wallet->balanceFiat()" />
                </div>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.wallet.supply')

                <x-general.loading-state.text :text="$wallet->balancePercentage()" />

                <div wire:loading.class="hidden">
                    {{ $wallet->balancePercentage() }}
                </div>
            </div>
        </div>
    @endforeach
</div>