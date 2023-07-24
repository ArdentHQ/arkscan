@props([
    'transaction',
    'title',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
])

@php
    $headerWidth = 'w-[87px]';
    if ($transaction->isVoteCombination()) {
        $headerWidth = 'w-[109px]';
    } elseif ($transaction->isLegacy()) {
        $headerWidth = 'w-[110px]';
    }
@endphp

<x-general.page-section.row
    :header-width="$headerWidth"
    :title="$title"
    :value="$value"
    :value-class="$valueClass"
    :tooltip="$tooltip"
>
    {{ $slot }}
</x-general.page-section.row>
