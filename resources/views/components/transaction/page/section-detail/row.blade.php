@props([
    'transaction',
    'title',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
    'allowEmpty' => false,
])

@php
    $headerWidth = 'w-[87px]';
    if ($transaction->hasPayload()) {
        $headerWidth = 'w-[132px]';
    }
@endphp

<x-general.page-section.row
    :header-width="$headerWidth"
    :title="$title"
    :value="$value"
    :value-class="$valueClass"
    :tooltip="$tooltip"
    :allow-empty="$allowEmpty"
>
    {{ $slot }}
</x-general.page-section.row>
