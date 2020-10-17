<div class="flex items-center ml-8">
    <div class="navbar-settings-button">
        <button
            @click="openDropdown = openDropdown === 'settings' ? null : 'settings'"
            class="inline-flex items-center justify-center py-2 transition duration-150 ease-in-out rounded-md text-theme-primary-300"
        >
            <span class="inline-flex">@svg('filter', 'h-5 w-5')</span>
        </button>
    </div>

    <div
        x-show.transition.origin.top="openDropdown === 'settings'"
        class="navbar-settings-dropdown"
        x-cloak
        wire:ignore
    >
        <div class="flex flex-col divide-y divide-dotted divide-theme-secondary-300">
            <x-navbar.setting-option title="Language" subtitle="Select display language">
                <select wire:model="language" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                    <option>English</option>
                </select>
            </x-navbar.setting-option>

            <x-navbar.setting-option title="Currency" subtitle="Select display currency">
                <select wire:model="currency" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                    <option>USD</option>
                </select>
            </x-navbar.setting-option>

            <x-navbar.setting-option title="Price Source" subtitle="Select price source">
                <select wire:model="priceSource" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                    <option>CoinGecko</option>
                </select>
            </x-navbar.setting-option>

            <x-navbar.setting-option title="Statistics Chart" subtitle="Enable/Disable statistics chart">
                <x-ark-toggle name="statisticsChart" hide-label />
            </x-navbar.setting-option>

            <x-navbar.setting-option title="Dark Theme" subtitle="Enable/Disable dark theme" no-border>
                <x-ark-toggle
                    name="darkTheme"
                    hide-label
                    alpine-click="$dispatch('toggle-dark-mode')"
                />
            </x-navbar.setting-option>
        </div>
    </div>
</div>
