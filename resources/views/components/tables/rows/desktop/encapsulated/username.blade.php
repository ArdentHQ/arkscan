@props([
    'model'
])

@if ($model->username())
    <span class="text-sm font-semibold leading-4.25">{{ $model->username() }}</span>
@endif
