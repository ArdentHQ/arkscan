@props([
    'title',
    'subtitle',
])

<div {{ $attributes->class('flex flex-col px-6 pt-8 pb-6 space-y-6 font-semibold md:px-10 md:mx-auto md:max-w-7xl') }}>
    <div class="flex flex-col space-y-1.5">
        <h1 class="mb-0 text-lg font-semibold md:text-2xl leading-5.25 md:leading-[1.8125rem]">
            {{ $title }}
        </h1>

        <span class="text-xs leading-5 text-theme-secondary-500 dark:text-theme-dark-500">
            {{ $subtitle }}
        </span>
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex flex-col space-y-2 sm:space-y-3 xl:flex-row xl:space-y-0 xl:space-x-3">
            {{ $slot }}
        </div>
    @endif
</div>
