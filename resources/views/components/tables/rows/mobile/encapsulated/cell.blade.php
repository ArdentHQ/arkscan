@props([
    'label' => null,
])

<div {{ $attributes->class('flex flex-col space-y-2 font-semibold text-theme-secondary-900 dark:text-theme-dark-200') }}>
    @if ($label)
        <span>
            {{ $label }}
        </span>
    @endif

    <div class="dark:text-theme-secondary-50">
        {{ $slot }}
    </div>
</div>
