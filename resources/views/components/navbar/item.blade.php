@props([
    'route',
    'label',
    'params' => [],
])

@php ($currentRoute = optional(Route::current())->getName())

<a
    href="{{ route($route, $params) }}"
    @class([
        'inline-flex font-semibold leading-5 group focus:outline-none focus:ring-inset transition duration-150 ease-in-out h-full px-2 mx-4 relative border-t-2 border-transparent rounded',
        'text-theme-secondary-900 dark:text-theme-dark-50' => $currentRoute === $route,
        'text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-200 dark:hover:text-theme-secondary-400' => $currentRoute !== $route,
    ])
>
    <span @class([
        'flex items-center w-full h-full border-b-2',
        'border-theme-primary-600' => $currentRoute === $route,
        'border-transparent group-hover:border-theme-primary-300 group-hover:dark:border-theme-dark-600' => $currentRoute !== $route,
    ])>
        <span>{{ $label }}</span>
    </span>
</a>
