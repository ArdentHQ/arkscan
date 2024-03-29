@props([
    'title',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
    'allowEmpty' => false,
])

<x-general.page-section.row
    header-width="w-[106px]"
    :title="$title"
    :value="$value"
    :value-class="$valueClass"
    :tooltip="$tooltip"
    :allow-empty="$allowEmpty"
>
    {{ $slot }}
</x-general.page-section.row>
