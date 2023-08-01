@props(['statistics'])

<div class="flex flex-col space-y-6 font-semibold px-6 pt-8 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
    <div class="flex flex-col space-y-0.5">
        <h1 class="text-lg md:text-2xl">@lang('pages.delegates.title')</h1>

        <span class="text-xs text-theme-secondary-500 dark:text-theme-dark-500">@lang('pages.delegates.subtitle')</span>
    </div>

    <div class="flex flex-col space-y-2 xl:flex-row xl:space-y-0 xl:space-x-2">
        <div class="flex flex-col space-y-2 sm:flex-row sm:space-y-0 sm:space-x-2 flex-1">
            <x-page-headers.delegates.missed-blocks :statistics="$statistics" />
            <x-page-headers.delegates.voting />
        </div>

        <x-page-headers.delegates.explore />
    </div>
</div>
