@props([
    'route',
    'label',
    'params' => [],
])

@php ($currentRoute = optional(Route::current())->getName())

<a
    href="{{ route($route, $params) }}"
    @class([
        'px-6 py-3 inline-flex font-semibold leading-5 group focus:outline-none transition-default h-full relative rounded hover:bg-theme-secondary-200',
        'text-theme-secondary-900 dark:text-theme-secondary-400' => $currentRoute === $route,
        'text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400' => $currentRoute !== $route,
    ])
>
    <span @class([
        'flex items-center w-full h-full text-theme-secondary-700 group-hover:text-theme-secondary-900 transition-default',
    ])>
        <span>{{ $label }}</span>
    </span>
</a>
