<div class="px-6 mt-8 md:px-10 md:mx-auto md:max-w-7xl md:border-0">
    <div class="flex flex-col rounded-xl border border-theme-secondary-300 md-lg:flex-row dark:border-theme-dark-700">
        <div class="flex flex-col flex-1 py-3 px-4 sm:px-6 md:py-6">
            <div class="flex justify-between items-center mb-5">
                <h2 class="mb-0 text-2xl font-semibold">
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

        <div class="flex-1 py-3 px-4 rounded-b-xl sm:px-6 md:py-6 bg-theme-secondary-100 md-lg:rounded-r-xl md-lg:rounded-bl-none dark:bg-theme-dark-950">
            {{-- @TODO: Update Chart --}}
            <livewire:network-status-block-price />
        </div>
    </div>
</div>
