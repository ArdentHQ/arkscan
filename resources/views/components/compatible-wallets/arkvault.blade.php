<div class="flex-col rounded-xl md:mt-6 md:border border-theme-secondary-300 dark:border-theme-secondary-800">
    <x-compatible-wallets.disclaimer
        class="inline-flex pb-6 w-full md:px-8 md:pt-6 xl:hidden"
        inner-class="w-full"
    />

    <div class="flex flex-col w-full min-[960px]:flex-row">
        <div class="flex flex-col flex-1 justify-center pb-4 sm:pb-8 md:px-8 xl:py-8">
            <x-compatible-wallets.disclaimer class="hidden xl:block" />

            <div class="xl:mt-6">
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

            <div class="flex flex-col justify-between py-3 px-3 mt-6 rounded-xl sm:flex-row md:pl-6 bg-theme-primary-50 dark:bg-theme-dark-blue-800">
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
                <div class="flex items-center mt-4 sm:h-auto h-15">
                    <a href="@lang('pages.compatible-wallets.arkvault.url')" target="_blank" rel="noopener nofollow noreferrer" class="flex items-center w-full h-full rounded-xl sm:mt-0 sm:w-auto md:mt-0 md:w-full lg:w-auto button-primary">
                        <div class="flex justify-center items-center h-full">
                            <span>
                                @lang('actions.learn_more')
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="flex flex-1 pt-2 pr-3 md:py-2 grow">
            <img src="{{ mix('images/wallets/arkvault.svg') }}" class="dark:hidden" />
            <img src="{{ mix('images/wallets/arkvault-dark.svg') }}" class="hidden dark:block" />
        </div>
    </div>
</div>
