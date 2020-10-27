<div class="space-y-8 divide-y md:hidden">
    @foreach ($transactions as $transaction)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <div class="flex justify-between w-full">
                @lang('general.transaction.id')

                <x-general.loading-state.text class="font-semibold">
                    <x-slot name="text">
                        <x-truncate-middle :value="$transaction->id()" />
                    </x-slot>
                </x-general.loading-state.text>

                <a href="{{ $transaction->url() }}" class="font-semibold link" wire:loading.class="hidden">
                    <x-truncate-middle :value="$transaction->id()" />
                </a>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.transaction.timestamp')

                <x-general.loading-state.text :text="$transaction->timestamp()" />

                <span wire:loading.class="hidden">{{ $transaction->timestamp() }}</span>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.transaction.sender')
                <x-general.address :address="$transaction->sender()" with-loading />
            </div>

            <div class="flex justify-between w-full">
                @lang('general.transaction.recipient')
                <x-general.address :address="$transaction->recipient() ?? $transaction->sender()" with-loading />
            </div>

            <div class="flex justify-between w-full">
                @lang('general.transaction.amount')

                <x-general.loading-state.text :text="$transaction->amount()" />

                <div wire:loading.class="hidden">
                    <x-general.amount-fiat-tooltip :amount="$transaction->amount()" :fiat="$transaction->amountFiat()" />
                </div>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.transaction.fee')
                <x-general.loading-state.text :text="$transaction->fee()" />

                <div wire:loading.class="hidden">
                    <x-general.amount-fiat-tooltip :amount="$transaction->fee()" :fiat="$transaction->feeFiat()" />
                </div>
            </div>
        </div>
    @endforeach
</div>