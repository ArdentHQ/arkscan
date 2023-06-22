@props([
    'model'
])

@if ($model->username())
    <span class="text-sm font-semibold leading-[17px]">{{ $model->username() }}</span>
@endif
