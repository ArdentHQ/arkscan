<div class="bg-theme-secondary-100 dark:bg-black">
    <div class="content-container-full-width md:py-16 md:px-8">
        <div class="flex flex-col w-full sm:space-y-5">
            <div class="flex items-center justify-between">
                <div class="hidden text-2xl font-bold whitespace-no-wrap text-theme-secondary-900 lg:block dark:text-theme-secondary-200">
                    @lang('general.search_explorer')
                </div>

                <livewire:network-status-block />
            </div>

            <div class="px-8 md:px-0">
                <livewire:search-module :is-advanced="$isAdvanced ?? false" />
            </div>
        </div>
    </div>
</div>
