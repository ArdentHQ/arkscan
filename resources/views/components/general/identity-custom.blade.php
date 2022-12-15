@props(['icon'])

<div class="flex flex-row-reverse items-center md:flex-row">
    {{ $icon }}

    <div class="mr-4 font-semibold md:mr-0 md:ml-4 text-theme-secondary-900 dark:text-theme-secondary-200">
        {{ $slot }}
    </div>
</div>
