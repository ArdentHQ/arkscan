@props([
    'model',
])

<span class="text-sm font-semibold leading-[17px]">
    {{ $model->transactionCount() }}
</span>
