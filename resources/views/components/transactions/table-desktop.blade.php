<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">@lang('general.transaction.id')</th>
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
            @foreach($transactions as $transaction)
                <tr>
                    <td>
                        <div class="flex items-center">
                            <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                            <a href="{{ $transaction->url() }}" class="mx-auto link" wire:loading.class="hidden">
                                @svg('link', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <span wire:loading.class="hidden">{{ $transaction->timestamp() }}</span>
                    </td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>
                        <x-general.address :address="$transaction->sender()" />
                    </td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>
                        <x-transactions.recipient :transaction="$transaction" />
                    </td>
                    <td class="text-right">
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                        </div>
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->fee()" :fiat="$transaction->feeFiat()" />
                        </div>
                    </td>
                    @isset($useConfirmations)
                        <td class="hidden text-right xl:table-cell">
                            <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                            @if($transaction->isConfirmed())
                                <span wire:loading.class="hidden">Confirmed</span>
                            @else
                                <span wire:loading.class="hidden">{{ $transaction->confirmations() }}/{{ Network::confirmations() }}</span>
                            @endif
                        </td>
                    @endisset
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
