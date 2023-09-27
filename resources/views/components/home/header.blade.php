<div class="px-6 md:px-10 md:mx-auto md:max-w-7xl md:border-0 mt-8">
    <div class="flex flex-col md-lg:flex-row border rounded-xl border-theme-secondary-300 dark:border-theme-dark-900">
        <div class="flex flex-col flex-1 py-3 md:py-6 px-4 sm:px-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-2xl mb-0">
                    @lang('pages.home.statistics.title')
                </h2>

                <a
                    href="{{ route('statistics') }}"
                    class="button-secondary px-4 py-1.5"
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

        <div class="flex-1 bg-theme-secondary-100 dark:bg-theme-dark-950 rounded-b-xl md-lg:rounded-r-xl">
            {{-- @TODO: Update Chart --}}
            <livewire:network-status-block-price />
        </div>
    </div>
</div>
