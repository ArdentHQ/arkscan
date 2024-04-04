@props([
    'model',
    'onClick' => null,
])

<x-validators.favorite-toggle
    :model="$model"
    :attributes="$attributes"
    :on-click="$onClick"
>
    @lang('tables.validator-monitor.favorite')
</x-validators.favorite-toggle>
