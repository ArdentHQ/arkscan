<div class="space-y-8 divide-y md:hidden">
    @foreach ($blocks as $block)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <div class="flex justify-between w-full">
                @lang('general.block.id')

                <x-general.loading-state.text class="font-semibold">
                    <x-slot name="text">
                        <x-truncate-middle :value="$block->id()" />
                    </x-slot>
                </x-general.loading-state.text>

                <a href="{{ $block->url() }}" class="font-semibold link" wire:loading.class="hidden">
                    <x-truncate-middle :value="$block->id()" />
                </a>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.block.timestamp')

                <x-general.loading-state.text :text="$block->timestamp()" />

                <span wire:loading.class="hidden">{{ $block->timestamp() }}</span>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.block.generated_by')

                <x-general.address :address="$block->delegateUsername()" with-loading />
            </div>

            <div class="flex justify-between w-full">
                @lang('general.block.height')

                <x-general.loading-state.text :text="$block->height()" />

                <span wire:loading.class="hidden">{{ $block->height() }}</span>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.block.transactions')

                <x-general.loading-state.text :text="$block->transactionCount()" />

                <span wire:loading.class="hidden">{{ $block->transactionCount() }}</span>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.block.amount')

                <x-general.loading-state.text :text="$block->amount()" />

                <div wire:loading.class="hidden">
                    <x-general.amount-fiat-tooltip :amount="$block->amount()" :fiat="$block->amountFiat()" />
                </div>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.block.fee')

                <x-general.loading-state.text :text="$block->fee()" />

                <div wire:loading.class="hidden">
                    <x-general.amount-fiat-tooltip :amount="$block->fee()" :fiat="$block->feeFiat()" />
                </div>
            </div>
        </div>
    @endforeach
</div>
