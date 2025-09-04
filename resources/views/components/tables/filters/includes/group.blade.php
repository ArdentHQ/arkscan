@props([
    'label' => null,
])

<div class="border-t border-theme-secondary-300 dark:border-theme-dark-800 pt-[0.125rem] mt-1">
    @if ($label)
        <div class="px-8 mt-3 mb-1 font-semibold leading-5 md:px-4 text-theme-secondary-900 group dark:text-theme-dark-200">
            {{ $label }}
        </div>
    @endif

    <div class="flex flex-col">
        {{ $slot }}
    </div>
</div>
