<div class="flex flex-col w-full rounded-xl sm:mt-6 sm:border min-[960px]:flex-row border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="flex flex-col flex-1 justify-center pb-8 space-y-6 sm:py-8 sm:px-8">
        <div>
            <span class="inline-flex text-xs items-center rounded font-semibold px-2 py-1 space-x-2 text-theme-primary-900 dark:text-white dark:bg-theme-dark-blue-700 arkvault-disclaimer-gradient">
                <x-ark-icon name="circle.info" size="sm" class="shrink-0" />
                <span>
                    @lang('pages.compatible-wallets.arkvault.disclaimer')
                </span>
            </span>
        </div>
        <div>
            <h2 class="text-2xl font-semibold text-theme-secondary-900">
                <span>@lang('general.arkvault') </span>
                <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                    (@lang('pages.compatible-wallets.arkvault.web_wallet'))
                </span>
            </h2>
            <p class="mt-2 leading-7 dark:text-theme-secondary-400">
                @lang('pages.compatible-wallets.arkvault.description')
            </p>
        </div>
        <div class="flex flex-col justify-between py-3 px-3 rounded-xl sm:flex-row sm:pl-6 bg-theme-primary-50 dark:bg-theme-dark-blue-800">
            <div class="flex flex-1 items-center py-1 bg-no-repeat bg-right sm:dark:bg-[url('/images/wallets/arrows-dark.svg')] sm:bg-[url('/images/wallets/arrows.svg')] min-[960px]:bg-none lg:dark:bg-[url('/images/wallets/arrows-dark.svg')] lg:bg-[url('/images/wallets/arrows.svg')] mr-2">
                <div>
                    <x-ark-icon name="app-wallets.arkvault" size="none" class="w-10 h-10 dark:text-white text-theme-navy-600" />
                </div>
                <div class="flex flex-col ml-3">
                    <span class="text-lg font-semibold dark:text-white text-theme-secondary-900">
                        @lang('general.arkvault')
                    </span>
                    <span class="text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-blue-400">
                        @lang('pages.compatible-wallets.arkvault.subtitle')
                    </span>
                </div>
            </div>
            <div class="flex items-center">
                <a href="@lang('pages.compatible-wallets.arkvault.url')" target="_blank" rel="noopener nofollow noreferrer" class="flex items-center mt-4 w-full h-full rounded-xl sm:mt-0 sm:w-auto md:mt-0 md:w-full lg:w-auto button-primary">
                    <div class="flex justify-center items-center h-full">
                        <span>
                            @lang('actions.learn_more')
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="flex flex-1 pt-2 pr-3 sm:py-2 grow">
        <img src="{{ mix('images/wallets/arkvault.svg') }}" class="dark:hidden" />
        <img src="{{ mix('images/wallets/arkvault-dark.svg') }}" class="hidden dark:block" />
    </div>
</div>
