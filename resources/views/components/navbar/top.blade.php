<div class="hidden relative z-40 bg-white md:flex md:flex-col dark:bg-theme-dark-900">
    <div class="flex justify-between items-center py-3 w-full content-container">
        <div class="flex items-center mr-3 whitespace-nowrap">
            <div class="flex font-semibold dark:text-white text-theme-secondary-900">
                <livewire:navbar.price-ticker />
            </div>
        </div>

        <div class="flex items-center space-x-3 md:w-full md-lg:w-auto">
            <livewire:navbar.search />

            <x-navbar.network-dropdown />

            <div
                x-data="ThemeManager()"
                @theme-changed.window="theme = $event.detail.theme"
            >
                <x-navbar.theme-dropdown />
            </div>

            <x-navbar.arkconnect />
        </div>
    </div>

    <div class="absolute bottom-0 w-full border-b border-theme-secondary-300 dark:border-theme-dark-700"></div>
</div>
