<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($payments as $payment)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="100">@lang('general.transaction.recipient')</td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$payment['recipientId']" />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.amount')</td>
                    <td>
                        <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            {{ $payment['amount'] }}
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
