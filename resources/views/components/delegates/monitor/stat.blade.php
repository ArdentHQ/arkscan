@props([
    'title',
    'value',
    'color',
])

<x-general.detail
    :title="$title"
    class="flex items-center space-x-2"
>
    <div @class([
        'rounded-full w-3 h-3',
        $color,
    ])></div>

    <span>{{ $value }}</span>
</x-general.detail>
