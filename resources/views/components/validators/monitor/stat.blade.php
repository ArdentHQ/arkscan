@props([
    'title',
    'value',
    'color',
    'loading' => false,
])

<x-general.detail
    :title="$title"
    class="flex items-center space-x-2"
>
    <div @class([
        'rounded-full w-3 h-3',
        $color,
    ])></div>

    @if ($loading)
        <x-loading.text
            width="w-[17px]"
            height="h-5"
        />
    @else
        <span>{{ $value }}</span>
    @endif
</x-general.detail>
