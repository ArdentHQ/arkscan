<div>
    @lang('labels.confirmations')

    @if($model->isConfirmed())
        <span class="flex items-center space-x-4">
            @svg('app-confirmations', 'w-5 h-5') <span>@lang('general.transaction.well-confirmed')</span>
        </span>
    @else
        <span>{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
    @endif
</div>
