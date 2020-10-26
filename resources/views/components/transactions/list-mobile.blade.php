<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.transaction.id')</td>
                    <td>
                        <x-general.loading-state.text class="font-semibold">
                            <x-slot name="text">
                                <x-truncate-middle :value="$transaction->id()" />
                            </x-slot>
                        </x-general.loading-state.text>

                        <a href="{{ $transaction->url() }}" class="font-semibold link" wire:loading.class="hidden">
                            <x-truncate-middle :value="$transaction->id()" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.timestamp')</td>
                    <td>
                        <x-general.loading-state.text :text="$transaction->timestamp()" />

                        <span wire:loading.class="hidden">{{ $transaction->timestamp() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.sender')</td>
                    <td>
                        <x-general.address :address="$transaction->sender()" with-loading />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.recipient')</td>
                    <td>
                        <x-general.address :address="$transaction->recipient() ?? $transaction->sender()" with-loading />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.amount')</td>
                    <td>
                        <x-general.loading-state.text :text="$transaction->amount()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.fee')</td>
                    <td>
                        <x-general.loading-state.text :text="$transaction->fee()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->fee()" :fiat="$transaction->feeFiat()" />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
