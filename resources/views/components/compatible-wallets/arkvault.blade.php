<x-general.card class="!p-0 border-0 md:border">
    <x-compatible-wallets.disclaimer
        class="inline-flex pb-6 w-full md:px-8 md:pt-6 xl:hidden"
        inner-class="w-full"
    />

    <div class="flex flex-col w-full md-lg:flex-row">
        <div class="flex flex-col flex-1 justify-center pb-4 sm:pb-8 md:px-8 xl:py-8">
            <x-compatible-wallets.disclaimer class="hidden xl:block" />

            <div class="xl:mt-6">
                <h2 class="text-lg font-semibold sm:text-2xl text-theme-secondary-900">
                    <span>@lang('general.arkvault') </span>
                    <span class="text-theme-secondary-500 dark:text-theme-dark-500">
                        (@lang('pages.compatible-wallets.arkvault.web_wallet'))
                    </span>
                </h2>

                <p class="mt-2 leading-7 dark:text-theme-dark-200">
                    @lang('pages.compatible-wallets.arkvault.description')
                </p>
            </div>

            <x-compatible-wallets.learn-more />
        </div>
        <div class="flex flex-1 pt-2 pr-3 md:py-2 grow">
            <img src="{{ mix('images/wallets/arkvault.svg') }}" class="dark:hidden" />
            <img src="{{ mix('images/wallets/arkvault-dark.svg') }}" class="hidden dark:block dim:hidden" />
            <img src="{{ mix('images/wallets/arkvault-dim.svg') }}" class="hidden dim:block" />
        </div>
    </div>
</x-general.card>
