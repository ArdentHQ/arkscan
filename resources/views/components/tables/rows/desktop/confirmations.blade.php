@if($model->isConfirmed())
    <span data-tippy-content="{{ trans_choice('general.confirmations', $model->confirmations(), ['count' => $model->confirmations()]) }}" class="flex justify-center"><x-general.circled-icon icon="app-confirmations" /></span>
@else
    <span>{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
@endif
