@props([
    'marginClass' => 'mx-1 md:mx-4',
])
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'items-center justify-center flex py-3 px-5 rounded text-theme-secondary-600 hover:text-theme-primary-700 hover:bg-theme-primary-100 focus:outline-none transition-default dark:text-theme-secondary-600 dark:hover:text-theme-secondary-100 dark:hover:bg-theme-secondary-800 ' . $marginClass
]) }}>
    {{ $slot }}
</button>
