<div class="flex items-center ml-8" wire:ignore>
    <div class="navbar-settings-button">
        <button
            @click="openDropdown = openDropdown === 'settings' ? null : 'settings'"
            class="inline-flex justify-center items-center py-2 rounded-md transition duration-150 ease-in-out text-theme-primary-300 hover:text-theme-primary-400 dark:text-theme-secondary-600 dark:hover:text-theme-secondary-500"
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
                <x-navbar.setting-option
                    :title="trans('forms.settings.currency.title')"
                    :subtitle="trans('forms.settings.currency.description')"
                    border
                >
                    <x-ark-rich-select
                        wire:model="state.currency"
                        wrapper-class="left-0 mt-3"
                        dropdown-class="right-0 mt-1 origin-top-right"
                        initial-value="{{ $this->state['currency'] ?? 'USD' }}"
                        placeholder="{{ $this->state['currency'] ?? 'USD' }}"
                        button-class="block font-medium text-left bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200"
                        :options="collect(config('currencies'))->keys()->mapWithKeys(function ($currency) {
                            return [$currency => config('currencies.' . $currency)['currency']];
                        })->toArray()"
                    />
                </x-navbar.setting-option>
            @endif

            <x-navbar.setting-option
                :title="trans('forms.settings.theme.title')"
                :subtitle="trans('forms.settings.theme.description')"
            >
                <x-ark-toggle
                    name="state.darkTheme"
                    hide-label
                    :default="$this->state['darkTheme'] ? 'true' : 'false'"
                    alpine-click="$dispatch('toggle-dark-mode')"
                />
            </x-navbar.setting-option>

            <x-navbar.setting-option
                :title="trans('forms.settings.table.title')"
                :subtitle="trans('forms.settings.table.description')"
            >
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
