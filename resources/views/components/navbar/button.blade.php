@props([
    'marginClass' => 'mx-1 md:mx-4',
    'disabled' => false,
])

<button
    {{ $attributes->merge([
        'type' => 'button',
    ])->class([
        'items-center justify-center flex p-2.5 rounded focus:ring-inset focus:outline-none transition-default',
        'text-theme-secondary-400' => $disabled,
        'text-theme-secondary-600 hover:text-theme-primary-700 hover:bg-theme-primary-100 dark:text-theme-dark-600 dark:hover:text-theme-secondary-100 dark:hover:bg-theme-secondary-800' => ! $disabled,
        $marginClass,
    ]) }}
    @if ($disabled)
        disabled
    @endif
>
    {{ $slot }}
</button>
