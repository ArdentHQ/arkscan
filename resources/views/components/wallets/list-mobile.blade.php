<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($wallets as $wallet)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="100">@lang('general.wallet.address')</td>
                    <td><x-general.address :address="$wallet->address()" /></td>
                </tr>
                @if ($wallet->isKnown() || $wallet->isOwnedByExchange())
                    <tr>
                        <td>@lang('general.wallet.info')</td>
                        <td>
                            <div class="flex flex-col space-y-4">
                                @if ($wallet->isKnown())
                                    <div class="flex items-center space-x-4">
                                        @svg('app-verified', 'w-5 h-5 text-theme-secondary-500')

                                        <span>@lang('general.verified_address')</span>
                                    </div>
                                @endif

                                @if ($wallet->isOwnedByExchange())
                                    <div class="flex items-center space-x-4">
                                        @svg('app-exchange', 'w-5 h-5 text-theme-secondary-500')

                                        <span>@lang('general.exchange')</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td>@lang('general.wallet.balance')</td>
                    <td>{{ $wallet->balance() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.wallet.supply')</td>
                    <td>{{ number_format($wallet->balancePercentage(), 2) }} %</td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
