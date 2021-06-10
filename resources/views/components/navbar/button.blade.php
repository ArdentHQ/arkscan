@props([
    'marginClass' => 'mx-1 md:mx-4',
])
<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'items-center justify-center flex p-4 rounded text-theme-secondary-600 hover:text-theme-primary-500 focus:outline-none transition-default dark:text-theme-secondary-600 dark:hover:text-theme-secondary-500 ' . $marginClass
]) }}>
    {{ $slot }}
</button>
