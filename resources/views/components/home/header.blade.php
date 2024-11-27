<div class="px-6 mt-8 md:px-10 md:mx-auto md:max-w-7xl md:border-0">
    <div class="flex flex-col lg:flex-row lg:space-x-3 space-y-3 lg:space-y-0">
        <div class="flex flex-col flex-1 py-3 px-4 sm:px-6 md:py-6 rounded-xl border border-theme-secondary-300 dark:border-theme-dark-700">
            <div class="flex justify-between items-center mb-3 md:mb-5">
                <h2 class="mb-0 text-xl font-semibold md:text-2xl">
                    @lang('pages.home.statistics.title')
                </h2>

                <a
                    href="{{ route('statistics') }}"
                    class="py-1.5 px-4 button-secondary"
                >
                    <div class="inline-flex items-center space-x-2">
                        <span>@lang('actions.view')</span>

                        <x-ark-icon
                            name="arrows.chevron-right-small"
                            size="xs"
                        />
                    </div>
                </a>
            </div>

            <livewire:home.statistics />
        </div>

        <div @class([
            "flex-1 bg-theme-secondary-100 dark:bg-theme-dark-950 rounded-xl border border-theme-secondary-300 dark:border-theme-dark-700",
            "py-3 px-4 sm:px-6 sm:pb-4 md:py-6" => Network::canBeExchanged(),
            "md-lg:px-6 md-lg:py-6" => ! Network::canBeExchanged(),
        ])>
            <div @class([
                'relative w-full h-full',
                'hidden md-lg:block' => ! Network::canBeExchanged(),
            ])>
                @if(! Network::canBeExchanged())
                    <div class="absolute top-1/2 left-1/2 text-sm font-semibold whitespace-nowrap -translate-x-1/2 -translate-y-1/2 text-theme-secondary-500 dark:text-theme-dark-400">
                        @lang('pages.home.statistics.chart_not_supported')
                    </div>
                @endif

                <div @class(["blur-md pointer-events-none" => !Network::canBeExchanged()])>
                    <livewire:home.chart />
                </div>
            </div>
        </div>
    </div>
</div>
