<div class="hidden table-container md:block">
    <table>
        <thead>
            <tr>
                <th class="text-center">@lang('general.block.id')</th>
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
                            <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                            <a href="{{ $block->url() }}" class="mx-auto link" wire:loading.class="hidden">
                                @svg('link', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        {{--TODO: Everything dissapear once we apply the wire:loading.class here, to investigate, might just be me locally --}}
                        <span wire:loading.class="hidden">{{ $block->timestamp() }}</span>
                    </td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$block->delegateUsername()" />
                    </td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <span wire:loading.class="hidden">{{ $block->height() }}<span>
                    </td>
                    <td>
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <span wire:loading.class="hidden">{{ $block->transactionCount() }}</span>
                    </td>
                    <td class="text-right">
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->amount()" :fiat="$block->amountFiat()" />
                        </div>
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

                        <div wire:loading.class="hidden">
                            <x-general.amount-fiat-tooltip :amount="$block->fee()" :fiat="$block->feeFiat()" />
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
