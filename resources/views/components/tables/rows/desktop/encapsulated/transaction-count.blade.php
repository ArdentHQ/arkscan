@props([
    'model',
])

<span class="text-sm font-semibold leading-4.25">
    {{ $model->transactionCount() }}
</span>
