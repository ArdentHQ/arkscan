@if($model->isConfirmed())
    <span class="flex justify-center"><x-general.circled-icon icon="app-confirmations" /></span>
@else
    <span>{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
@endif
