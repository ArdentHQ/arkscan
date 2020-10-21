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
                    <td><x-general.address :address="$wallet->address()" /></td>
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
                    <td class="text-right">{{ $wallet->balance() }}</td>
                    <td class="hidden text-right lg:table-cell">{{ number_format($wallet->balancePercentage(), 2) }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
