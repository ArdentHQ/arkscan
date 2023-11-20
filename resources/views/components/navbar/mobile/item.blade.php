@props([
    'route',
    'label',
    'params' => [],
])

@php ($currentRoute = optional(Route::current())->getName())

<a
    href="{{ route($route, $params) }}"
    @class([
        'inline-flex font-semibold leading-5 group focus:outline-none transition duration-150 ease-in-out h-full relative py-3 w-full',
        'text-theme-secondary-900 dark:text-theme-dark-200 border-l-4 border-theme-primary-600 dark:bg-black bg-theme-primary-50 w-full' => $currentRoute === $route,
        'text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-200 dark:hover:text-theme-secondary-400 hover:background-theme-secondary-200' => $currentRoute !== $route,
    ])
>
    <span @class([
        'flex items-center w-full h-full',
        'pl-5' => $currentRoute === $route,
        'pl-6' => $currentRoute !== $route,
    ])>
        <span>{{ $label }}</span>
    </span>
</a>
