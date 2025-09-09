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
        'px-5 py-[0.875rem] my-[0.125rem] font-semibold transition-default cursor-pointer leading-5 rounded-lg',
        'text-theme-secondary-500 bg-theme-secondary-200 dark:bg-theme-secondary-900 dark:text-theme-dark-500' => $disabled,
        'bg-theme-secondary-200 dark:bg-theme-dark-950 text-theme-primary-600 dark:text-theme-dark-50' => $isActive && ! $disabled,
        'text-theme-secondary-700 hover:dark:text-theme-dark-50 hover:text-theme-secondary-900 hover:bg-theme-secondary-200 hover:dark:bg-theme-dark-950 dark:text-theme-dark-200' => ! $isActive && ! $disabled,
    ]) }}
>
    {{ $slot }}
</a>
