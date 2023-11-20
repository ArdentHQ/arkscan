@props([
    'label' => null,
])

<div {{ $attributes->class('flex flex-col space-y-2 font-semibold leading-4.25') }}>
    @if ($label)
        <span class="dark:text-theme-dark-200">
            {{ $label }}
        </span>
    @endif

    <div class="text-theme-secondary-900 dark:text-theme-dark-50">
        {{ $slot }}
    </div>
</div>
