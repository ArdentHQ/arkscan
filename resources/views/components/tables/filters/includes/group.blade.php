@props(['label'])

<div>
    <div class="font-semibold mt-3 mb-1 px-4 leading-5 dark:text-theme-secondary-200">
        {{ $label }}
    </div>

    <div class="flex flex-col space-y-2">
        {{ $slot }}
    </div>
</div>
