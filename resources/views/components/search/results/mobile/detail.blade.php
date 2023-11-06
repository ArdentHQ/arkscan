@props(['title'])

<x-general.detail
    :title="$title"
    title-class="text-xs !leading-3.75"
    class="text-xs !leading-3.75"
>
    {{ $slot }}
</x-general.detail>
