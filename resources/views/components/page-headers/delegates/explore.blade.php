<x-page-headers.header-item
    :attributes="$attributes"
    class="flex-none xl:flex-1 bg-theme-primary-100 dark:bg-theme-dark-800 dim:!bg-theme-dark-700"
>
    <x-slot name="background" class="left-0 right-auto sm:right-0 sm:left-auto">
        <img
            src="{{ mix('images/delegates/header-bg.svg') }}"
            class="hidden max-w-none sm:block dark:hidden"
        />

        <img
            src="{{ mix('images/delegates/header-bg-mobile.svg') }}"
            class="max-w-none sm:hidden dark:hidden"
        />

        <img
            src="{{ mix('images/delegates/header-bg-dark.svg') }}"
            class="hidden max-w-none dark:sm:block dim:sm:!hidden"
        />

        <img
            src="{{ mix('images/delegates/header-bg-mobile-dark.svg') }}"
            class="hidden max-w-none dark:block dark:sm:hidden dim:!hidden"
        />

        <img
            src="{{ mix('images/delegates/header-bg-dim.svg') }}"
            class="hidden max-w-none dark:hidden dim:sm:block"
        />

        <img
            src="{{ mix('images/delegates/header-bg-mobile-dim.svg') }}"
            class="hidden max-w-none dark:hidden dim:!block dim:sm:!hidden"
        />
    </x-slot>

    <div class="absolute top-0 right-0 z-10 w-full h-full bg-gradient-to-t sm:bg-gradient-to-r dim:bg-gradient-to-b from-theme-primary-100 to-theme-primary-200 dim:sm:bg-gradient-to-l sm:w-[400px] dark:from-theme-dark-800 dark:to-theme-dark-700"></div>

    <div class="flex relative z-30 flex-col flex-1 items-center space-y-3 sm:flex-row sm:justify-between sm:space-y-0">
        <div class="flex flex-col space-y-1.5 w-full">
            <div class="text-sm md:text-lg text-theme-primary-900 md:leading-5.25 dark:text-theme-dark-50">
                @lang('pages.delegates.explore.title')
            </div>

            <div class="text-xs leading-5 text-theme-secondary-700 sm:leading-3.75 dark:text-theme-dark-200">
                @lang('pages.delegates.explore.subtitle')
            </div>
        </div>

        <div class="w-full sm:w-auto">
            <x-ark-external-link
                class="!flex items-center space-x-2 button-primary py-1.5 px-4 justify-center"
                :url="trans('urls.docs.validator')"
                :title="trans('actions.explore')"
                inner-class="leading-5"
                icon-class="inline relative flex-shrink-0 text-white"
                icon-size="sm"
            >
                <span>@lang('pages.delegates.explore.action')</span>
            </x-ark-external-link>
        </div>
    </div>
</x-page-headers.header-item>
