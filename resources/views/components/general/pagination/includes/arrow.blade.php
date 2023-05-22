@props([
    'page',
    'icon',
    'disabled' => false,
    'text' => null,
])

<div class="flex-1 sm:flex-none sm:w-8 md:w-auto">
    <button
        {{ $attributes->class([
            'items-center button-secondary flex justify-center h-8 p-0 w-full',
            'sm:w-8' => ! $text,
            'w-8 md:w-auto md:px-4' => $text,
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
</div>
