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
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$wallet->address()" />
                    </td>
                    @if ($hasInfo)
                        <td class="text-center">
                            <div class="flex items-center justify-center space-x-2 text-theme-secondary-500">
                                @if ($wallet->isKnown())
                                    <div data-tippy-content="@lang('general.verified_address')">
                                        @svg('app-verified', 'w-5 h-5')
                                    </div>
                                @endif

                                @if ($wallet->isOwnedByExchange())
                                    <div data-tippy-content="@lang('general.exchange')">
                                        @svg('app-exchange', 'w-5 h-5')
                                    </div>
                                @endif
                            </div>
                        </td>
                    @endif
                    <td class="text-right">
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$wallet->balance()" :fiat="$wallet->balanceFiat()" />
                        </div>
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            {{ number_format($wallet->balancePercentage(), 2) }} %
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
