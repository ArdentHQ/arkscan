<div class="flex items-center" wire:ignore>
    <x-navbar.separator />

    <x-navbar.button
        margin-class="ml-1 -mr-2 md:ml-4 md:-mr-4"
        @click="showSettings = !showSettings"
        dusk="navigation-search-modal-trigger"
    >
        <span class="inline-flex">
            <x-ark-icon name="sliders-vertical"/>
        </span>
    </x-navbar.button>

    <div
        x-show.transition.origin.top="showSettings"
        class="fixed top-0 right-0 left-0 z-30 px-4 pt-3 mt-20 w-full md:absolute md:left-auto md:p-0 md:mt-24 md:w-120"
        x-cloak
        @click.away="showSettings = false"
    >
        <div class="p-8 bg-white rounded-xl shadow-xl md:p-10 dark:bg-theme-secondary-900">
            <div class="flex flex-col">
                @if(Network::canBeExchanged())
                    <x-navbar.setting-option
                        :title="trans('forms.settings.currency.title')"
                        :subtitle="trans('forms.settings.currency.description')"
                        border
                    >
                        <x-ark-rich-select
                            wire:model="state.currency"
                            wrapper-class="left-0 mt-3 mr-4"
                            dropdown-class="right-0 mt-1 origin-top-right"
                            initial-value="{{ Str::lower($this->state['currency'] ?? 'USD') }}"
                            placeholder="{{ $this->state['currency'] ?? 'USD' }}"
                            button-class="block font-medium text-left bg-transparent text-theme-secondary-700 dark:text-theme-secondary-200"
                            icon-class="flex absolute inset-y-0 right-0 justify-center items-center -mr-4"
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
                        default="window.getThemeMode() === 'dark'"
                    />
                </x-navbar.setting-option>

                <x-navbar.setting-option
                    :title="trans('forms.settings.table.title')"
                    :subtitle="trans('forms.settings.table.description')"
                    breakpoint="md"
                >
                    <x-ark-toggle
                        name="state.compactTables"
                        hide-label
                        :default="$this->state['compactTables'] ? 'false' : 'true'"
                        alpine-click="$dispatch('toggle-compact-table', { expand: value })"
                    />
                </x-navbar.setting-option>
            </div>
        </div>
    </div>
</div>
