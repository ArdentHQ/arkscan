<div class="flex pt-8 space-y-6 content-container">
    <div class="flex flex-row justify-center w-full p-8 rounded-lg sm:justify-between h-30 lg:h-32 bg-theme-primary-600">
        <div class="flex flex-row items-center justify-between space-x-4">
            <div class="flex items-center">
                <div class="w-12 h-12 border-white circled-icon text-theme-secondary-400">
                    <x-ark-icon name="app-marketsquare" size="sm" />
                </div>
            </div>

            <div class="flex flex-col justify-between space-y-2 font-semibold md:ml-4">
                <div class="text-sm leading-tight text-theme-primary-300 dark:text-theme-secondary-700">
                    @lang('general.more_details', ['transactionType' => $transaction->typeLabel()])
                </div>

                <div class="flex items-center space-x-2 leading-tight">
                    <span class="text-white dark:text-theme-secondary-200">@lang('general.learn_more') <span class="hidden sm:contents">@lang('generic.at') MarketSquare</span></span>
                    <a href="{{ $transaction->marketSquareLink() }}" class="mx-auto link">
                        <x-ark-icon name="link" size="sm" />
                    </a>
                </div>
            </div>
        </div>

        <div class="flex-row items-center hidden lg:flex">
            <div class="relative inline-block text-white">
                <x-ark-icon name="app-marketsquare" class="h-6 ml-2 text-white md:h-8 lg:h-12 lg:ml-0" />
            </div>
            <span class="ml-4 text-3xl">
                <span class="font-bold text-white">Market</span><span class="text-white">Square</span>
            </span>
        </div>
    </div>
</div>
