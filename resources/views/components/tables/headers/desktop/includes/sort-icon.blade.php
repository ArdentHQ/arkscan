@props([
    'id',
    'initialDirection',
    'disabled' => false,
])

@if ($id !== null)
    <div
        @class([
            'flex flex-col text-theme-secondary-500 dark:text-theme-dark-500',
            'group-hover/header:text-theme-secondary-900 group-hover/header:dark:text-theme-dark-50' => ! $disabled
        ])

        @unless ($disabled)
            x-cloak
        @endunless
    >
        <div
            class="transition-default"
            :class="{
                'text-theme-primary-600 dark:text-theme-dark-blue-400': sortBy === '{{ $id }}' && sortAsc,
            }"
        >
            <x-ark-icon
                name="arrows.caret-up"
                size="w-2 h-2"
            />
        </div>

        <div
            class="transition-default"
            :class="{
                'text-theme-primary-600 dark:text-theme-dark-blue-400': sortBy === '{{ $id }}' && ! sortAsc,
                'text-theme-secondary-500 dark:text-theme-dark-500': sortBy !== '{{ $id }}',
            }"
        >
            <x-ark-icon
                name="arrows.caret-down"
                size="w-2 h-2"
            />
        </div>
    </div>
@endif
