<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th width="50">&nbsp;</th>
                <th><span class="pl-14">@lang('general.delegates.name')</span></th>
                <th width="200" class="hidden text-right lg:table-cell">@lang('general.delegates.votes')</th>
            </tr>
        </thead>
        <tbody>
            <x-skeleton>
                <tr>
                    <td>
                        <x-tables.rows.desktop.skeleton.resignation-id />
                    </td>
                    <td>
                        <x-tables.rows.desktop.skeleton.username />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.skeleton.votes />
                    </td>
                </tr>
            </x-skeleton>
        </tbody>
    </table>
</div>
