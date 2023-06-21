@props(['label'])

<div>
    <div class="px-4 mt-3 mb-1 font-semibold leading-5 text-theme-secondary-900 dark:text-theme-secondary-200 group">
        {{ $label }}
    </div>

    <div class="flex flex-col space-y-2">
        {{ $slot }}
    </div>
</div>
