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
                <select wire:model="state.language" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                    <option value="en">English</option>
                </select>
            </x-navbar.setting-option>

            @if(Network::canBeExchanged())
                <x-navbar.setting-option title="Currency" subtitle="Select display currency">
                    <select wire:model="state.currency" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                        <option value="aud">AUD</option>
                        <option value="brl">BRL</option>
                        <option value="btc">BTC</option>
                        <option value="cad">CAD</option>
                        <option value="chf">CHF</option>
                        <option value="cny">CNY</option>
                        <option value="eth">ETH</option>
                        <option value="eur">EUR</option>
                        <option value="gbp">GBP</option>
                        <option value="jpy">JPY</option>
                        <option value="krw">KRW</option>
                        <option value="ltc">LTC</option>
                        <option value="nzd">NZD</option>
                        <option value="rub">RUB</option>
                        <option value="usd">USD</option>
                    </select>
                </x-navbar.setting-option>

                <x-navbar.setting-option title="Price Source" subtitle="Select price source">
                    <select wire:model="state.priceSource" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                        <option value="cryptocompare">CryptoCompare</option>
                    </select>
                </x-navbar.setting-option>
            @endif

            <x-navbar.setting-option title="Statistics Chart" subtitle="Enable/Disable statistics chart">
                <x-ark-toggle name="state.statisticsChart" hide-label />
            </x-navbar.setting-option>

            <x-navbar.setting-option title="Dark Theme" subtitle="Enable/Disable dark theme" no-border>
                <x-ark-toggle
                    name="state.darkTheme"
                    hide-label
                    alpine-click="$dispatch('toggle-dark-mode')"
                />
            </x-navbar.setting-option>
        </div>
    </div>
</div>
