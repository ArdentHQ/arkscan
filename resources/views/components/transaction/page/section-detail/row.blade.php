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
    if ($transaction->isBlsRegistration()) {
        if ($transaction->oldBlsPublicKey()) {
            $headerWidth = 'w-[155px]';
        } else {
            $headerWidth = 'w-[116px]';
        }
    } elseif ($transaction->isVoteCombination()) {
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
    :allow-empty="$allowEmpty"
>
    {{ $slot }}
</x-general.page-section.row>
