<x-page-headers.header-item
    :attributes="$attributes"
    class="flex-none -mx-6 mt-6 -mb-8 rounded-none md:mx-0 md:mb-0 xl:flex-1 bg-theme-primary-100 dim:bg-theme-dark-700 sm:h-[140px] md:h-[150px] md-lg:h-[193px] lg:h-[206px] xl:h-[264px] dark:bg-theme-dark-800"
    content-class="p-6 h-full sm:py-0"
    slot-class="h-full"
    without-padding
>
    <x-slot
        name="background"
        class="left-0 right-auto max-w-none sm:right-0 sm:left-auto"
    >
        <img
            src="{{ mix('images/home/footer-bg.svg') }}"
            class="max-w-none sm:block dark:hidden"
        />

        <img
            src="{{ mix('images/home/footer-bg-dark.svg') }}"
            class="hidden max-w-none dark:block dim:hidden"
        />

        <img
            src="{{ mix('images/home/footer-bg-dim.svg') }}"
            class="hidden max-w-none dim:block"
        />
    </x-slot>

    <div class="absolute top-0 right-0 z-10 w-full h-full bg-gradient-to-t sm:w-3/4 sm:bg-gradient-to-r dim:bg-gradient-to-b from-theme-primary-100 to-theme-primary-200 dim:sm:bg-gradient-to-l dark:from-theme-dark-800 dark:to-theme-dark-700"></div>

    <div class="flex relative z-30 flex-col flex-1 items-center h-full sm:flex-row sm:justify-between">
        <div class="hidden -ml-24 h-full sm:block md:-ml-16 lg:ml-0">
            <img src="{{ mix('images/home/footer.svg') }}" class="h-full dark:hidden" />
            <img src="{{ mix('images/home/footer-dark.svg') }}" class="hidden h-full dark:block dim:hidden" />
            <img src="{{ mix('images/home/footer-dim.svg') }}" class="hidden h-full dim:block" />
        </div>

        <div class="flex flex-col flex-1 w-full sm:ml-6 sm:w-auto md:ml-2 lg:ml-6 md-lg:pl-8">
            <div class="hidden md-lg:block">
                <div class="text-2xl font-semibold lg:text-3xl lg:font-bold lg:leading-10 leading-[29px] text-theme-primary-900 dark:text-theme-dark-50">
                    @lang('pages.home.footer.title')
                </div>

                <div class="mt-2 text-sm font-semibold lg:text-base lg:leading-5 text-theme-secondary-800 dark:text-theme-dark-200">
                    @lang('pages.home.footer.subtitle')
                </div>
            </div>

            <x-compatible-wallets.learn-more
                background-color="bg-[#F5FAFF]/30 dark:bg-[#505D6A]/30 dim:bg-[#476DB0]/30 backdrop-blur"
                class="border border-theme-primary-300 dark:border-theme-dark-500"
                padding="p-6 sm:p-3 lg:px-6 md-lg:mt-5"
                title-color="text-theme-primary-900 dark:text-theme-dark-50"
                subtitle-color="text-theme-secondary-800 dark:text-theme-dark-200 text-xs"
                icon-size="w-11 h-11"
                arrows-breakpoint="xl"
                home
            />
        </div>
    </div>
</x-page-headers.header-item>
