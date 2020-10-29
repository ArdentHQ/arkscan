@if($model->isConfirmed())
    <span>@lang('general.confirmed')</span>
@else
    <span>{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
@endif
