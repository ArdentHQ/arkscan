@if($shortened ?? false)
    <span data-tippy-content="{{ $model->timestamp() }}">{{ $model->timestamp(true) }}</span>
@else
    {{ $model->timestamp() }}
@endif
