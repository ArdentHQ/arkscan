@props([
    'color' => 'text-theme-secondary-300 dark:text-theme-dark-700',
])

<div class="hidden flex-col px-6 sm:flex md:px-10 md:mx-auto md:max-w-7xl">
    <hr {{ $attributes->class([
        'h-px hidden md:block',
        $color,
    ]) }} />
</div>
