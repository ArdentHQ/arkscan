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
        'px-6 inline-flex font-semibold leading-5 group focus:outline-none focus:ring-inset transition-default h-full relative hover:dark:bg-theme-dark-950 hover:bg-theme-secondary-200 rounded-lg py-[0.875rem] my-[0.125rem]',
        'text-theme-secondary-900' => $currentRoute === $route,
        'text-theme-secondary-700 hover:text-theme-secondary-900 hover:dark:text-theme-dark-50' => $currentRoute !== $route,
    ])
>
    <span @class([
        'flex items-center w-full h-full text-theme-secondary-700 dark:text-theme-dark-200 dark:group-hover:text-white group-hover:text-theme-secondary-900 transition-default',
    ])>
        <span>{{ $label }}</span>
    </span>
</a>
