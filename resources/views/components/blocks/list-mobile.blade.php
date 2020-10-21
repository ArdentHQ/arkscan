<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($blocks as $block)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td width="150">@lang('general.block.id')</td>
                    <td>
                        <a href="{{ $block->url() }}" class="font-semibold link">{{ $block->id() }}</a>
                    </td>
                </tr>
                <tr>
                    <td>@lang('general.block.timestamp')</td>
                    <td>{{ $block->timestamp() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.block.generated_by')</td>
                    <td><x-general.address :address="$block->delegate()" /></td>
                </tr>
                <tr>
                    <td>@lang('general.block.height')</td>
                    <td>{{ $block->height() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.block.transactions')</td>
                    <td>{{ $block->transactionCount() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.block.amount')</td>
                    <td>{{ $block->amount() }}</td>
                </tr>
                <tr>
                    <td>@lang('general.block.fee')</td>
                    <td>{{ $block->fee() }}</td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
