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
                        <x-tables.rows.desktop.address :model="$wallet" :without-truncate="$withoutTruncate ?? false" />
                    </td>
                    {{-- @TODO: this is a code smell. We should have separate views instead of littering everything with if/else to hide UI elements. --}}
                    @if ($hasInfo)
                        <td class="text-center">
                            <x-tables.rows.desktop.wallet-type :model="$wallet" />
                        </td>
                    @endif
                    <td class="text-right">
                        <x-tables.rows.desktop.balance :model="$wallet" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
