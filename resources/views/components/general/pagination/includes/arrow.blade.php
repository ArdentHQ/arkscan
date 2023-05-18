@props([
    'page',
    'icon',
    'disabled' => false,
    'text' => null,
])

<button
    {{ $attributes->class([
        'items-center button-secondary pagination-button-mobile w-8 h-8 p-0',
        'md:w-auto md:px-4' => $text,
    ]) }}
    wire:click="gotoPage({{ $page }})"
    x-on:click="hideSearch"
    @if ($disabled)
        disabled
    @endif
>
    <x-ark-icon
        :name="$icon"
        size="xs"
        :class="Arr::toCssClasses([
            'md:hidden' => $text,
        ])"
    />

    @if ($text)
        <span class="hidden md:inline">
            {{ $text }}
        </span>
    @endif
</button>
