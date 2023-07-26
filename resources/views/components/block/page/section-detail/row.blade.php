@props([
    'title',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
])

<x-general.page-section.row
    header-width="w-[106px]"
    :title="$title"
    :value="$value"
    :value-class="$valueClass"
    :tooltip="$tooltip"
>
    {{ $slot }}
</x-general.page-section.row>
