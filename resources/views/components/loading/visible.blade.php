@props([
    'displayType' => null,
])

@php
    if ($displayType !== null) {
        $displayType = [
            'block' => 'block',
            'inline-block' => 'inline-block',
        ][$displayType] ?? 'inline-block';
    }
@endphp

<div
    class="w-full"
    @if ($displayType)
        wire:loading.{{ $displayType }}
    @else
        wire:loading
    @endif
>
    {{ $slot }}
</div>
