@props([
    'radius' => '25',
    'stroke' => '2',
    'circleColor' => null,
    'progress' => '25',
])

@php
$normalizedRadius = $radius - $stroke * 2;
$circumference = $normalizedRadius * 2 * pi();
$strokeDashoffset = $circumference - $progress / 100 * $circumference;
@endphp

<span
    class="flex relative justify-center items-center transition rotate-minus-90"
    style="height: {{ $radius * 2 }}px; width: {{ $radius * 2 }}px; margin: -{{ $stroke }}px"
>

    <svg
        height="{{ $radius * 2}}"
        width="{{ $radius * 2}}"
        class="absolute forging-status"
    >
        <circle
            fill="transparent"
            stroke-dasharray="{{ $circumference }} {{ $circumference }}"
            stroke-width="{{ $stroke }}"
            r="{{ $normalizedRadius }}"
            cx="{{ $radius }}"
            cy="{{ $radius }}"
            class="absolute"
        ></circle>
        <circle
            stroke="var(--theme-color-{{ $circleColor }})"
            fill="transparent"
            stroke-dasharray="{{ $circumference }} {{ $circumference }}"
            style="stroke-dashoffset: {{ $strokeDashoffset }};"
            stroke-width="{{ $stroke }}"
            r="{{ $normalizedRadius }}"
            cx="{{ $radius }}"
            cy="{{ $radius }}"
        ></circle>
    </svg>

    {{ $slot }}
</span>