@props([
    'id',
    'data',
    'labels',
    'canvasClass' => '',
    'width' => '1000',
    'height' => '500',
    'grid' => false,
    'tooltips' => false,
    'theme' => collect(['name' => 'grey', 'mode' => 'light']),
    'currency' => \App\Services\Settings::currency(),
])

<div
    x-data="CustomChart(
        '{{ $id }}',
        {{ $data }},
        {{ $labels }},
        '{{ $grid }}',
        '{{ $tooltips }}',
        {{ $theme }},
        '{{ time() }}',
        '{{ $currency }}',
    )"
    x-init="init"
    @stats-period-updated.window="updateChart"
    wire:key="{{ $id.time() }}"
    {{ $attributes->only('class') }}
>
    <div wire:ignore class="relative w-full h-full">
        <canvas
            x-ref="{{ $id }}"
            @if($canvasClass) class="{{ $canvasClass }}" @endif
            @if($width) width="{{ $width }}" @endif
            @if($height) height="{{ $height }}" @endif
        ></canvas>
    </div>
</div>
