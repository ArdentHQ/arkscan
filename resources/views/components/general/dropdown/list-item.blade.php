@props([
    'url' => null,
    'isActive' => false,
])

<a
    @click="isOpen = false"
    @if ($url)
        href="{{ $url }}"
    @endif
    {{ $attributes->class([
        'border-l-4 pl-5 pr-6 py-3 font-semibold hover:text-theme-secondary-900 hover:bg-theme-secondary-200 dark:hover:bg-theme-secondary-900 dark:text-theme-dark-50 transition-default cursor-pointer leading-5',
        'border-theme-primary-600 bg-theme-primary-50 dark:bg-theme-dark-900 text-theme-secondary-900 dark:text-white' => $isActive,
        'border-transparent text-theme-secondary-700' => ! $isActive,
    ]) }}
>
    {{ $slot }}
</a>
