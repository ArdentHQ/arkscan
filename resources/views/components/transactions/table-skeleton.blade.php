<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center"></th>
                <th class="hidden lg:table-cell">@lang('general.transaction.timestamp')</th>
                <th><span class="pl-24">@lang('general.transaction.sender')</span></th>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th class="text-right">@lang('general.transaction.amount')</th>
                <th class="hidden text-right xl:table-cell">@lang('general.transaction.fee')</th>
                @isset($useConfirmations)
                    <th class="hidden text-right xl:table-cell">@lang('general.transaction.confirmations')</th>
                @endisset
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
                        <div class="flex flex-row-reverse items-center justify-between md:flex-row md:space-x-3 md:justify-start">
                            <div>
                                <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 loading-state"></div>
                            </div>
                            <div class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        </div>
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
