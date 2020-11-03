<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th><span class="pl-14">@lang('general.wallet.address')</span></th>
                <th class="text-center">@lang('general.wallet.info')</th>
                <th class="text-right">@lang('general.wallet.balance')</th>
                <th width="120" class="hidden text-right lg:table-cell">@lang('general.wallet.supply')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($wallets as $wallet)
                <tr>
                    <td wire:key="{{ $wallet->id() }}-address">
                        <x-tables.rows.desktop.address :model="$wallet" :without-truncate="$withoutTruncate ?? false" />
                    </td>
                    <td class="text-center">
                        <x-tables.rows.desktop.wallet-type :model="$wallet" />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.balance :model="$wallet" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        @isset($useVoteWeight)
                            <x-tables.rows.desktop.vote-percentage :model="$wallet" />
                        @else
                            <x-tables.rows.desktop.balance-percentage :model="$wallet" />
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
