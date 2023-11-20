@props([
    'route',
    'label',
    'params' => [],
    'url' => null,
])

@php ($currentRoute = optional(Route::current())->getName())

<a
    href="{{ $route ? route($route, $params) : $url }}"
    target="{{ $route ? '_self' : '_blank'}}"
    @class([
        'ml-6 border-l border-theme-secondary-300 dark:border-theme-dark-700 px-6 py-3 inline-flex font-semibold leading-5 group focus:outline-none transition-default w-full h-full relative dark:hover:bg-theme-dark-900 hover:bg-theme-secondary-200',
        'text-theme-secondary-900 dark:text-theme-dark-50' => $currentRoute === $route,
        'text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-dark-50 dark:hover:text-theme-secondary-50' => $currentRoute !== $route,
    ])
>
    <span @class([
        'flex items-center w-full h-full text-theme-secondary-700 dark:text-theme-dark-50 dark:group-hover:text-white group-hover:text-theme-secondary-900 transition-default',
    ])>
        <span>{{ $label }}</span>
    </span>
</a>
