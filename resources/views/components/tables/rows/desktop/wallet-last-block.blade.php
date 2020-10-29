@if($model->lastBlock())
    <a href="{{ route('block', $model->lastBlock()['id']) }}" class="font-semibold link">
        <x-truncate-middle :value="$model->lastBlock()['id']" />
    </a>
@else
    n/a
@endif
