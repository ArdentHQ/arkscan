<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($blocks as $block)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.block.id')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <a href="{{ $block->url() }}" class="font-semibold link" wire:loading.class="hidden">
                            <x-truncate-middle :value="$block->id()" />
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.timestamp')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <span wire:loading.class="hidden">{{ $block->timestamp() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.generated_by')</td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$block->delegateUsername()" />
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.height')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <span wire:loading.class="hidden">{{ $block->height() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.transactions')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <span wire:loading.class="hidden">{{ $block->transactionCount() }}</span>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.amount')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->amount()" :fiat="$block->amountFiat()" />
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.fee')</td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->fee()" :fiat="$block->feeFiat()" />
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
