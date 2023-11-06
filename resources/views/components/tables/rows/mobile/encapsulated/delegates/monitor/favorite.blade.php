@props([
    'model',
    'onClick' => null,
])

<x-delegates.favorite-toggle
    :model="$model"
    :attributes="$attributes"
    :on-click="$onClick"
>
    @lang('tables.delegate-monitor.favorite')
</x-delegates.favorite-toggle>
