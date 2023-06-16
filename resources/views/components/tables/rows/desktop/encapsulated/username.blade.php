@props([
    'model'
])

@if ($model->username())
    <span class="font-semibold text-sm leading-[17px]">{{ $model->username() }}</span>
@endif
