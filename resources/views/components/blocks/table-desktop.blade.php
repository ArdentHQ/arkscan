<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center"></th>
                <th class="hidden lg:table-cell">@lang('general.block.timestamp')</th>
                <th><span class="pl-14">@lang('general.block.generated_by')</span></th>
                <th>@lang('general.block.height')</th>
                <th>
                    <div class="inline-block">
                        <span class="hidden lg:block">@lang('general.block.transactions')</span>
                        <span class="lg:hidden">@lang('general.block.tx')</span>
                    </div>
                </th>
                <th class="text-right">@lang('general.block.amount')</th>
                <th class="hidden text-right xl:table-cell">@lang('general.block.fee')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($blocks as $block)
                <tr>
                    <td>
                        <div class="flex items-center">
                            <x-general.loading-state.icon icon="link" class="mx-auto" />

                            <a href="{{ $block->url() }}" class="mx-auto link" wire:loading.class="hidden">
                                @svg('app-details', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">
                        <x-general.loading-state.text :text="$block->timestamp()" />

                        <span wire:loading.class="hidden">{{ $block->timestamp() }}</span>
                    </td>
                    <td>
                        <x-general.address :address="$block->delegateUsername()" with-loading />
                    </td>
                    <td>
                        <x-general.loading-state.text :text="$block->height()" />

                        <span wire:loading.class="hidden">{{ $block->height() }}<span>
                    </td>
                    <td>
                        <x-general.loading-state.text :text="$block->transactionCount()" />

                        <span wire:loading.class="hidden">{{ $block->transactionCount() }}</span>
                    </td>
                    <td class="text-right">
                        <x-general.loading-state.text :text="$block->amount()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->amount()" :fiat="$block->amountFiat()" />
                        </div>
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-general.loading-state.text :text="$block->fee()" />

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->fee()" :fiat="$block->feeFiat()" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
