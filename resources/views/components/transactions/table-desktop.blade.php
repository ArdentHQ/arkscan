<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">@lang('general.transaction.id')</th>
                <th class="hidden lg:table-cell">@lang('general.transaction.timestamp')</th>
                <th class="hidden text-center xl:table-cell">@lang('general.transaction.type')</th>
                <th><span class="pl-14">@lang('general.transaction.sender')</span></th>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th class="text-right">@lang('general.transaction.amount')</th>
                <th class="hidden text-right xl:table-cell">@lang('general.transaction.fee')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr>
                    <td>
                        <div class="flex items-center">
                            <a href="{{ $transaction->url() }}" class="mx-auto link">
                                @svg('link', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">{{ $transaction->timestamp() }}</td>
                    <td class="hidden xl:table-cell">
                        <div class="flex items-center justify-center w-10 h-10 mx-auto border-2 rounded-full text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                            @svg('app-transactions.'.$transaction->iconType(), 'w-4 h-4')
                        </div>
                    </td>
                    <td><x-general.address :address="$transaction->sender()" /></td>
                    <td><x-general.address :address="$transaction->recipient() ?? $transaction->sender()" /></td>
                    <td class="text-right">
                        <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-general.amount-fiat-tooltip :amount="$transaction->fee()" :fiat="$transaction->feeFiat()" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
