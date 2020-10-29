<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">&nbsp;</th>
                <th class="hidden lg:table-cell">@lang('general.transaction.timestamp')</th>
                <th><span class="pl-14">@lang('general.transaction.sender')</span></th>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th class="text-right">@lang('general.transaction.amount')</th>
                <th class="hidden text-right xl:table-cell">@lang('general.transaction.fee')</th>
                @isset($useConfirmations)
                    <th class="hidden text-right xl:table-cell">@lang('general.transaction.confirmations')</th>
                @endisset
            </tr>
        </thead>
        <tbody>
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.transaction-id />
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-tables.rows.desktop.skeleton.timestamp />
                    </td>
                    <td>
                        @isset($useDirection)
                            <x-tables.rows.desktop.skeleton.sender-with-direction />
                        @else
                            <x-tables.rows.desktop.skeleton.sender />
                        @endif
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.recipient />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.skeleton.amount />
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-tables.rows.desktop.skeleton.fee />
                    </td>
                    @isset($useConfirmations)
                    <td class="hidden text-right xl:table-cell">
                        <x-tables.rows.desktop.skeleton.confirmations />
                    </td>
                    @endisset
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
