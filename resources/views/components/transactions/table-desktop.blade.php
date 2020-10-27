<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">@lang('general.transaction.id')</th>
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
            @foreach($transactions as $transaction)
                <tr>
                    <td>
                        <div class="flex items-center">
                            <x-general.loading-state.icon icon="link" class="mx-auto" />

                            <a href="{{ $transaction->url() }}" class="mx-auto link" wire:loading.class="hidden">
                                @svg('link', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-general.loading-state.text :text="$transaction->timestamp()" />

                        <span wire:loading.class="hidden">{{ $transaction->timestamp() }}</span>
                    </td>
                    <td>
                        @isset($useDirection)
                            <x-transactions.sender :transaction="$transaction" :wallet="$wallet" with-loading />
                        @else
                            <x-general.address :address="$transaction->sender()" with-loading />
                        @endif
                    </td>
                    <td>
                        <x-transactions.recipient :transaction="$transaction" with-loading />
                    </td>
                    <td class="text-right">
                        <x-general.loading-state.text :text="$transaction->amount()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                        </div>
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-general.loading-state.text :text="$transaction->fee()" />

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
