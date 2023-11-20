@props([
    'label' => null,
])

<div>
    @if ($label)
        <div class="px-8 mt-3 mb-1 font-semibold leading-5 md:px-4 text-theme-secondary-900 group dark:text-theme-dark-200">
            {{ $label }}
        </div>
    @endif

    <div class="flex flex-col space-y-2">
        {{ $slot }}
    </div>
</div>
