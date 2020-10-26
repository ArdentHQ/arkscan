<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($blocks as $block)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.block.id')</td>
                    <td>
                        <x-general.loading-state.text class="font-semibold">
                            <x-slot name="text">
                                <x-truncate-middle :value="$block->id()" />
                            </x-slot>
                        </x-general.loading-state.text>

                        <a href="{{ $block->url() }}" class="font-semibold link" wire:loading.class="hidden">
                            <x-truncate-middle :value="$block->id()" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.timestamp')</td>
                    <td>
                        <x-general.loading-state.text :text="$block->timestamp()" />

                        <span wire:loading.class="hidden">{{ $block->timestamp() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.generated_by')</td>
                    <td>
                        <x-general.address :address="$block->delegateUsername()" with-loading />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.height')</td>
                    <td>
                        <x-general.loading-state.text :text="$block->height()" />

                        <span wire:loading.class="hidden">{{ $block->height() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.transactions')</td>
                    <td>
                        <x-general.loading-state.text :text="$block->transactionCount()" />

                        <span wire:loading.class="hidden">{{ $block->transactionCount() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.amount')</td>
                    <td>
                        <x-general.loading-state.text :text="$block->amount()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->amount()" :fiat="$block->amountFiat()" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.fee')</td>
                    <td>
                        <x-general.loading-state.text :text="$block->fee()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->fee()" :fiat="$block->feeFiat()" />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
