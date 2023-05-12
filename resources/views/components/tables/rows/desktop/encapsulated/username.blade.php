@props([
    'model'
])

@if ($model->username())
    <span class="font-semibold">{{ $model->username() }}</span>
@endif
