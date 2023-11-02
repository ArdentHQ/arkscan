@props(['model'])

<x-delegates.favorite-toggle
    :model="$model"
    :attributes="$attributes"
>
    @lang('tables.delegate-monitor.favorite')
</x-delegates.favorite-toggle>
