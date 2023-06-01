@props([
    'id',
    'initialDirection',
])

@if ($id !== null)
    <div
        class="transition-default group-hover/header:text-theme-secondary-700"
        :class="{
            'text-theme-secondary-500': sortBy === '{{ $id }}',
            'text-transparent': sortBy !== '{{ $id }}',
            'rotate-180': (sortBy === '{{ $id }}' && sortAsc === false) || (sortBy !== '{{ $id }}' && '{{ $initialDirection }}' === 'desc'),
        }"
        x-cloak
    >
        <x-ark-icon
            name="arrows.chevron-up-small"
            size="w-2.5 h-2.5"
        />
    </div>
@endif
