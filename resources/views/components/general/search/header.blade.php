<div class="flex flex-col w-full space-y-5">
    <div class="flex items-center justify-between">
        <div class="hidden text-2xl font-bold whitespace-no-wrap text-theme-secondary-900 lg:block dark:text-theme-secondary-200">
            @lang('general.search_explorer')
        </div>

        <livewire:network-status-block />
    </div>

    <div class="px-8 md:px-0">
        <livewire:search-module />
    </div>
</div>
