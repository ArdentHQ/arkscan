@props([
    'displayType' => null,
    'targets'     => null,
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
    {{ $attributes->class('w-full') }}

    @if ($displayType)
        wire:loading.{{ $displayType }}
    @else
        wire:loading
    @endif

    @if ($targets)
        wire:target="{{ implode(',', $targets) }}"
    @endif
>
    {{ $slot }}
</div>
