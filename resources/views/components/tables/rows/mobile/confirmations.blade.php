<div>
    @lang('labels.confirmations')

    @if($model->isConfirmed())
        <span class="flex items-center space-x-4">
            <x-icon name="app-confirmations" /> <span>@lang('general.transaction.well-confirmed')</span>
        </span>
    @else
        <span>{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
    @endif
</div>
