<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($transactions as $transaction)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.transaction.id')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <a href="{{ $transaction->url() }}" class="font-semibold link" wire:loading.class="hidden">
                            <x-truncate-middle :value="$transaction->id()" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.timestamp')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <span wire:loading.class="hidden">{{ $transaction->timestamp() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.sender')</td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$transaction->sender()" />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.recipient')</td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$transaction->recipient() ?? $transaction->sender()" />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.amount')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.transaction.fee')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$transaction->fee()" :fiat="$transaction->feeFiat()" />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
