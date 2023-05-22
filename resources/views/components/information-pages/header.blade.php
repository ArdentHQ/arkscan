@props([
    'title',
    'subtitle',
])

<div class="flex flex-col justify-between space-y-3 sm:space-y-4 md-lg:flex-row md-lg:items-center md-lg:space-y-0">
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
