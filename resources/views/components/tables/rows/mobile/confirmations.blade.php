<div>
    @lang('labels.confirmations')

    @if($model->isConfirmed())
        <span class="flex items-center space-x-4">
            <span>@lang('general.transaction.well-confirmed')</span>
            <x-ark-icon name="app-confirmations" />
        </span>
    @else
        <span>{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
    @endif
</div>
