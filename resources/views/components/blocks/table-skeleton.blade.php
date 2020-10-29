<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center"></th>
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
            @for ($i = 0; $i < 15; $i++)
                <tr>
                    <td>
                        <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                    </td>
                    <td class="hidden lg:table-cell">
                        <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                    </td>
                    <td>
                        <div class="flex flex-row-reverse items-center justify-between md:flex-row md:space-x-3 md:justify-start">
                            <div>
                                <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
                            </div>
                            <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        </div>
                    </td>
                    <td>
                        <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                    </td>
                    <td>
                        <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                    </td>
                    <td class="text-right">
                        <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                    </td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
