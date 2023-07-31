@props([
    'targets' => null,
])

<div
    class="w-full"
    wire:loading.remove

    @if ($targets)
        wire:target="{{ implode(',', $targets) }}"
    @endif
>
    {{ $slot }}
</div>
