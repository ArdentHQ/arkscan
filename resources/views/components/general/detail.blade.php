@props([
    'title',
    'value' => null,
])

<div class="flex flex-col space-y-2 font-semibold">
    <div class="text-sm whitespace-nowrap text-theme-secondary-700 dark:text-theme-dark-200">
        {{ $title }}
    </div>

    <div {{ $attributes->class('text-theme-secondary-900 dark:text-theme-dark-50 leading-5') }}>
        @if ($value)
            {{ $value }}
        @else
            {{ $slot }}
        @endif
    </div>
</div>
