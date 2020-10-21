<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($blocks as $block)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">
                        <div>@lang('general.block.id')</div>
                    </td>
                    <td>
                        <div><a href="{{ $block->url() }}" class="font-semibold link">{{ $block->id() }}</a></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.block.timestamp')</div>
                    </td>
                    <td>
                        <div>{{ $block->timestamp() }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.block.generated_by')</div>
                    </td>
                    <td>
                        <div><x-general.address :address="$block->delegate()" /></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.block.height')</div>
                    </td>
                    <td>
                        <div>{{ $block->height() }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.block.transactions')</div>
                    </td>
                    <td>
                        <div>{{ $block->transactionCount() }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.block.amount')</div>
                    </td>
                    <td>
                        <div>{{ $block->amount() }}</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div>@lang('general.block.fee')</div>
                    </td>
                    <td>
                        <div>{{ $block->fee() }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
