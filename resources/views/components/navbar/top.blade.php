<div class="hidden sticky top-0 z-30 bg-white md:flex md:flex-col dark:bg-theme-secondary-900">
    <div class="flex justify-between items-center py-3 w-full content-container">
        <div class="flex items-center">
            @if(Network::canBeExchanged())
                <div class="hidden font-semibold md:flex dark:text-white text-theme-secondary-900">
                    <livewire:price-ticker />
                </div>
            @endif
        </div>

        <div class="flex space-x-3">
            <x-ark-input-with-prefix
                icon="magnifying-glass"
                type="text"
                id="search"
                name="search"
                :placeholder="trans('general.navbar.search_placeholder')"
                container-class="flex pl-1 bg-theme-secondary-200 dark:bg-theme-secondary-800"
                wrapper-class-override="relative rounded shadow-sm"
                field-class-override="block w-full border-0 rounded outline-none appearance-none transition-default pl-2 pr-4 py-2 text-sm leading-4 placeholder:text-theme-secondary-700 text-theme-secondary-900 dark:text-theme-secondary-400 bg-theme-secondary-200 dark:bg-theme-secondary-800"
                hide-label
                disable-dirty-styling
                iconSize="sm"
            />

            <livewire:navbar.dark-mode-toggle
                active-icon="underline-moon"
                inactive-icon="underline-sun"
                setting="darkTheme"
            />
        </div>
    </div>

    <div class="absolute bottom-0 w-full border-b border-theme-secondary-300 dark:border-theme-secondary-800"></div>
</div>
