@props([
    'title',
    'value' => null,
    'loading' => false,
])

<div class="flex flex-col space-y-2 font-semibold">
    <div class="text-sm text-theme-secondary-700 dark:text-theme-dark-200 whitespace-nowrap leading-4.25">
        {{ $title }}
    </div>

    <div {{ $attributes->class('text-theme-secondary-900 dark:text-theme-dark-50 leading-5') }}>
        @if ($loading)
            <x-loading.text height="h-5" />
        @elseif ($value)
            {{ $value }}
        @else
            {{ $slot }}
        @endif
    </div>
</div>
