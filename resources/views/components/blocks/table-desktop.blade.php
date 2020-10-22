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
                            <a href="{{ $block->url() }}" class="mx-auto link">
                                @svg('link', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td class="hidden lg:table-cell">{{ $block->timestamp() }}</td>
                    <td><x-general.address :address="$block->delegate()" /></td>
                    <td>{{ $block->height() }}</td>
                    <td>{{ $block->transactionCount() }}</td>
                    <td class="text-right">
                        <x-general.amount-fiat-tooltip :amount="$block->amount()" :fiat="$block->amountFiat()" />
                    </td>
                    <td class="hidden text-right xl:table-cell">
                        <x-general.amount-fiat-tooltip :amount="$block->fee()" :fiat="$block->feeFiat()" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
