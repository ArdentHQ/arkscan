@php ($hasInfo = false)

@foreach ($wallets as $wallet)
    @if ($wallet->isKnown() || $wallet->isOwnedByExchange())
        @php ($hasInfo = true)
        @break
    @endif
@endforeach

<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th><span class="pl-14">@lang('general.wallet.address')</span></th>
                @if ($hasInfo)
                    <th class="text-center">@lang('general.wallet.info')</th>
                @endif
                <th class="text-right">@lang('general.wallet.balance')</th>
                <th width="120" class="hidden text-right lg:table-cell">@lang('general.wallet.supply')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wallets as $wallet)
                <tr>
                    <td>
                        <x-general.address :address="$wallet->address()" with-loading />
                    </td>
                    @if ($hasInfo)
                        <td class="text-center">
                            <div class="flex items-center justify-center space-x-2 text-theme-secondary-500">
                                @if ($wallet->isKnown())
                                    <x-general.loading-state.icon icon="app-verified" />

                                    <div data-tippy-content="@lang('general.verified_address')"  wire:loading.class="hidden">
                                        @svg('app-verified', 'w-5 h-5')
                                    </div>
                                @endif

                                @if ($wallet->isOwnedByExchange())
                                    <x-general.loading-state.icon icon="app-exchange" />

                                    <div data-tippy-content="@lang('general.exchange')"  wire:loading.class="hidden">
                                        @svg('app-exchange', 'w-5 h-5')
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endif
                    <td class="text-right">
                        <x-general.loading-state.text :text="$wallet->balance()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$wallet->balance()" :fiat="$wallet->balanceFiat()" />
                        </div>
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-general.loading-state.text :text="$wallet->balancePercentage()" />

                        <div wire:loading.class="hidden">
                            {{ $wallet->balancePercentage() }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
