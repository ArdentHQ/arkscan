@props([
    'page',
    'icon',
    'disabled' => false,
    'text' => null,
])

<div>
    <button
        {{ $attributes->class([
            'items-center button-secondary flex justify-center flex-1 w-8 h-8 p-0 sm:flex-initial',
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
</div>
