<div class="flex items-center ml-8" wire:ignore>
    <div class="navbar-settings-button">
        <button
            @click="openDropdown = openDropdown === 'settings' ? null : 'settings'"
            class="inline-flex items-center justify-center py-2 transition duration-150 ease-in-out rounded-md text-theme-primary-300 hover:text-theme-primary-400 dark:text-theme-secondary-600 dark:hover:text-theme-secondary-500"
        >
            <span class="inline-flex">
                <x-ark-icon name="filter" size="sm" />
            </span>
        </button>
    </div>

    <div
        x-show.transition.origin.top="openDropdown === 'settings'"
        class="navbar-settings-dropdown"
        x-cloak
    >
        <div class="flex flex-col">
            @if(Network::canBeExchanged())
                <x-navbar.setting-option :title="trans('forms.settings.currency.title')" :subtitle="trans('forms.settings.currency.description')">
                    <select wire:model="state.currency" class="font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                        <option value="AUD">AUD</option>
                        <option value="BRL">BRL</option>
                        <option value="BTC">BTC</option>
                        <option value="CAD">CAD</option>
                        <option value="CHF">CHF</option>
                        <option value="CNY">CNY</option>
                        <option value="ETH">ETH</option>
                        <option value="EUR">EUR</option>
                        <option value="GBP">GBP</option>
                        <option value="JPY">JPY</option>
                        <option value="KRW">KRW</option>
                        <option value="LTC">LTC</option>
                        <option value="NZD">NZD</option>
                        <option value="RUB">RUB</option>
                        <option value="USD">USD</option>
                    </select>
                </x-navbar.setting-option>

                <x-navbar.setting-option :title="trans('forms.settings.price_chart.title')" :subtitle="trans('forms.settings.price_chart.description')">
                    <x-ark-toggle
                        name="state.priceChart"
                        hide-label
                        :default="$this->state['priceChart'] ? 'true' : 'false'"
                        alpine-click="$dispatch('toggle-price-chart')"
                    />
                </x-navbar.setting-option>
            @endif

            <x-navbar.setting-option :title="trans('forms.settings.fee_chart.title')" :subtitle="trans('forms.settings.fee_chart.description')" breakpoint="sm">
                <x-ark-toggle
                    name="state.feeChart"
                    hide-label
                    :default="$this->state['feeChart'] ? 'true' : 'false'"
                    alpine-click="$dispatch('toggle-fee-chart')"
                />
            </x-navbar.setting-option>

            <x-navbar.setting-option :title="trans('forms.settings.theme.title')" :subtitle="trans('forms.settings.theme.description')">
                <x-ark-toggle
                    name="state.darkTheme"
                    hide-label
                    :default="$this->state['darkTheme'] ? 'true' : 'false'"
                    alpine-click="$dispatch('toggle-dark-mode')"
                />
            </x-navbar.setting-option>

            <x-navbar.setting-option :title="trans('forms.settings.table.title')" :subtitle="trans('forms.settings.table.description')">
                <x-ark-toggle
                    name="state.compactTables"
                    hide-label
                    :default="$this->state['compactTables'] ? 'true' : 'false'"
                    alpine-click="$dispatch('toggle-compact-table')"
                />
            </x-navbar.setting-option>
        </div>
    </div>
</div>
