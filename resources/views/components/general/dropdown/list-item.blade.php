@props([
    'url' => null,
    'isActive' => false,
    'disabled' => false,
])

<a
    @click="isOpen = false"
    @if ($url)
        @if ($isActive)
            href="javascript:void(0)"
        @else
            href="{{ $url }}"
        @endif
    @endif
    {{ $attributes->class([
        'border-l-4 pl-5 pr-6 py-3 font-semibold transition-default cursor-pointer leading-5',
        'hover:text-theme-secondary-900 hover:bg-theme-secondary-200 dark:hover:bg-theme-secondary-900 dark:text-theme-dark-50' => ! $disabled,
        'text-theme-secondary-500 bg-theme-secondary-200 dark:bg-theme-secondary-900 dark:text-theme-dark-500' => $disabled,
        'border-theme-primary-600 dark:border-theme-dark-blue-500 bg-theme-primary-50 dark:bg-theme-dark-900 text-theme-secondary-900 dark:text-white' => $isActive && ! $disabled,
        'border-transparent' => ! $isActive,
        'text-theme-secondary-700' => ! $isActive && ! $disabled,
    ]) }}
>
    {{ $slot }}
</a>
