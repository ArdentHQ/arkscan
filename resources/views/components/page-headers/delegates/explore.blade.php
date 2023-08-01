<x-page-headers.delegates.header-item
    :attributes="$attributes"
    class="flex-none xl:flex-1 bg-theme-primary-100 dark:bg-theme-dark-800"
>
    <x-slot name="background" class="right-auto left-0 sm:right-0 sm:left-auto">
        <img
            src="{{ mix('images/delegates/header-bg.svg') }}"
            class="hidden sm:block dark:hidden max-w-none"
        />

        <img
            src="{{ mix('images/delegates/header-bg-mobile.svg') }}"
            class="sm:hidden dark:hidden max-w-none"
        />

        <img
            src="{{ mix('images/delegates/header-bg-dark.svg') }}"
            class="hidden dark:sm:block max-w-none"
        />

        <img
            src="{{ mix('images/delegates/header-bg-mobile-dark.svg') }}"
            class="hidden dark:block dark:sm:hidden max-w-none"
        />
    </x-slot>

    <div class="absolute right-0 w-full sm:w-[400px] top-0 h-full bg-gradient-to-t sm:bg-gradient-to-r from-theme-primary-100 to-theme-primary-200 dark:from-theme-dark-800 dark:to-theme-dark-700 z-10"></div>

    <div class="relative flex flex-col space-y-3 sm:flex-row sm:space-y-0 sm:justify-between items-center flex-1 z-30">
        <div class="flex flex-col space-y-1.5 w-full">
            <div class="text-sm sm:text-lg text-theme-primary-900 dark:text-theme-dark-50 sm:leading-5.25">
                @lang('pages.delegates.explore.title')
            </div>

            <div class="text-xs leading-3.75 dark:text-theme-dark-200">
                @lang('pages.delegates.explore.subtitle')
            </div>
        </div>

        <div class="w-full sm:w-auto">
            <x-ark-external-link
                class="!flex items-center space-x-2 button-primary py-1.5 px-4 justify-center"
                url="#"
                title="Explore"
                inner-class="leading-5"
                icon-class="inline relative flex-shrink-0 text-white"
                icon-size="sm"
            >
                <span>@lang('pages.delegates.explore.action')</span>
            </x-ark-external-link>
        </div>
    </div>
</x-delegates.header-item>
