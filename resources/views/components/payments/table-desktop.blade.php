<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th width="180" class="hidden text-right lg:table-cell">@lang('general.transaction.amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$payment['recipientId']" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            {{ $payment['amount'] }}
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
