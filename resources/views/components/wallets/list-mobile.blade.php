<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($wallets as $wallet)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="100">@lang('general.wallet.address')</td>
                    <td>
                        <x-general.address :address="$wallet->address()" with-loading />
                    </td>
                </tr>
                @if ($wallet->isKnown() || $wallet->isOwnedByExchange())
                    <tr>
                        <td>@lang('general.wallet.info')</td>
                        <td>
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
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>@lang('general.wallet.balance')</td>
                    <td>
                        <x-general.loading-state.text :text="$wallet->balance()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$wallet->balance()" :fiat="$wallet->balanceFiat()" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.wallet.supply')</td>
                    <td>
                        <x-general.loading-state.text :text="$wallet->balancePercentage()" />

                        <div wire:loading.class="hidden">
                            {{ $wallet->balancePercentage() }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
