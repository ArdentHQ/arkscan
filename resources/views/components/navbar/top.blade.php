<div class="hidden relative z-40 bg-white md:flex md:flex-col dark:bg-theme-dark-900">
    <div class="flex justify-between items-center py-3 w-full content-container">
        <div class="flex items-center mr-3 whitespace-nowrap">
            <div class="flex font-semibold dark:text-white text-theme-secondary-900">
                <livewire:price-ticker />
            </div>
        </div>

        <div class="flex items-center space-x-3 md:w-full md-lg:w-auto">
            <livewire:navbar.search />

            <x-navbar.network-dropdown/>

            <livewire:navbar.dark-mode-toggle
                active-icon="underline-moon"
                inactive-icon="underline-sun"
                setting="darkTheme"
            />
        </div>
    </div>

    <div class="absolute bottom-0 w-full border-b border-theme-secondary-300 dark:border-theme-dark-800"></div>
</div>
