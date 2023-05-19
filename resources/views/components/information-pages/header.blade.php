@props([
    'title',
    'subtitle',
])

<div class="flex flex-col justify-between items-center md-lg:flex-row">
    <div class="flex flex-col">
        <h1 class="text-lg font-semibold sm:text-2xl xl:mb-1.5 text-theme-secondary-900">
            {{ $title }}
        </h1>

        <span class="text-xs font-semibold text-theme-secondary-500">
            {{ $subtitle }}
        </span>
    </div>

    {{ $slot }}
</div>
