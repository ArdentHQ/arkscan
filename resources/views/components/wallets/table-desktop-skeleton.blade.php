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
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.address />
                    </td>
                    <td class="text-center">
                        <x-tables.rows.desktop.skeleton.wallet-type />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.skeleton.balance />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.skeleton.balance-percentage />
                    </td>
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
