<x-page-headers.delegates.header-item
    :attributes="$attributes"
    class="flex-none xl:flex-1 bg-[#E5F0F8] dark:bg-[#363B43]"
>
    <x-slot name="background" class="right-auto left-0 sm:right-0 sm:left-auto">
        @svg('app-delegates.header-bg', ['class' => 'hidden sm:block dark:hidden'])
        @svg('app-delegates.header-bg-mobile', ['class' => 'sm:hidden dark:hidden'])
        @svg('app-delegates.header-bg-dark', ['class' => 'hidden dark:sm:block'])
        @svg('app-delegates.header-bg-mobile-dark', ['class' => 'hidden dark:block dark:sm:hidden'])
    </x-slot>

    <div class="absolute right-0 w-full sm:w-[400px] top-0 h-full bg-gradient-to-t sm:bg-gradient-to-r from-[#E5F0F8] to-[#BAD6F0] dark:from-[#363B43] dark:to-[#3D444D] z-10"></div>

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
