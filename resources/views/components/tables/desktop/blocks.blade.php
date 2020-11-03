<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">&nbsp;</th>
                <th class="hidden lg:table-cell">@lang('general.block.timestamp')</th>
                @if(!isset($withoutGenerator))
                    <th><span class="pl-14">@lang('general.block.generated_by')</span></th>
                @endif
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
            @foreach($blocks as $block)
                <tr>
                    <td>
                        <x-tables.rows.desktop.block-id :model="$block" />
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-tables.rows.desktop.timestamp :model="$block" />
                    </td>
                    @if(!isset($withoutGenerator))
                        <td wire:key="{{ $block->id() }}-forger">
                            <x-tables.rows.desktop.block-forger :model="$block" />
                        </td>
                    @endif
                    <td>
                        <x-tables.rows.desktop.block-height :model="$block" />
                    </td>
                    <td>
                        <x-tables.rows.desktop.transaction-count :model="$block" />
                    </td>
                    <td class="text-right">
                        <x-tables.rows.desktop.amount :model="$block" />
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-tables.rows.desktop.fee :model="$block" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
