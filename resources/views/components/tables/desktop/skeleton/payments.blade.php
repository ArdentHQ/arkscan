<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th width="180" class="hidden text-right lg:table-cell">@lang('general.transaction.amount')</th>
            </tr>
        </thead>
        <tbody>
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.recipient />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.skeleton.amount />
                    </td>
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
