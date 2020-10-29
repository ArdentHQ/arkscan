<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">&nbsp;</th>
                <th class="hidden lg:table-cell">@lang('general.block.timestamp')</th>
                <th><span class="pl-14">@lang('general.block.generated_by')</span></th>
                <th>@lang('general.block.height')</th>
                <th>
                    <div class="inline-block">
                        <span class="hidden lg:block">@lang('general.block.transactions')</span>
                        <span class="lg:hidden">@lang('general.block.tx')</span>
                    </div>
                </th>
                <th class="text-right">@lang('general.block.amount')</th>
                <th class="hidden text-right xl:table-cell">@lang('general.block.fee')</th>
            </tr>
        </thead>
        <tbody>
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.block-id />
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-tables.rows.desktop.skeleton.timestamp />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.block-forger />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.block-height />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.transaction-count />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.skeleton.amount />
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-tables.rows.desktop.skeleton.fee />
                    </td>
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
