{{-- TODO: Tidy up fields - review compared to design to see if they can be improved --}}
<div
    x-data="{
        showAdvanced: false,
        isMobileOpen: false,
    }"
    @mobile-search.window="isMobileOpen = true"
    class="searchbar"
    x-bind:class="{
        'search-mobile': isMobileOpen,
        'search-advanced': showAdvanced,
    }"
>
    <div
        class="fixed inset-0 z-30 overflow-y-auto opacity-75 bg-theme-secondary-900 md:hidden"
        @click="isMobileOpen = false"
    ></div>

    <div class="search-container">
        <div class="flex items-center px-8 py-6 space-x-8">
            <div class="flex-1">
                <input
                    type="text"
                    placeholder="@lang('forms.search.term_placeholder')"
                    class="hidden w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 sm:block"
                    wire:model="state.term"
                    wire:keydown.enter="performSearch"
                />

                <input
                    type="text"
                    placeholder="@lang('forms.search.term_placeholder_mobile')"
                    class="w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 sm:hidden"
                    wire:model="state.term"
                    wire:keydown.enter="performSearch"
                />
            </div>

            <div
                class="hidden text-theme-secondary-900 dark:text-theme-secondary-600 md:block"
                x-on:click="showAdvanced = !showAdvanced"
            >
                <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
                <span x-show="showAdvanced">@lang('actions.hide_search')</span>
            </div>

            <button
                type="button"
                class="hidden button-primary md:block"
                wire:click="performSearch"
            >
                @lang('actions.find_it')
            </button>

            <div
                class="text-theme-primary-300 md:hidden"
                wire:click="performSearch"
            >
                @svg('search', 'h-5 w-5')
            </div>
        </div>

        <div
            x-show="showAdvanced"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 transform"
            x-transition:enter-end="opacity-100 transform"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 transform"
            x-transition:leave-end="opacity-0 transform"
        >
            {{-- <div class="flex flex-wrap items-center py-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800"> --}}
            <div class="search-advanced-options">
                <x-general.search.advanced-option :title="trans('forms.search.type')">
                    {{-- TODO: Enum of types and their values? --}}
                    <select class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                        <option value="">Multisignature Registration</option>
                    </select>
                </x-general.search.advanced-option>

                <x-general.search.advanced-option :title="trans('forms.search.amount_range')">
                    <div class="flex items-center space-x-2">
                        <input
                            type="number"
                            placeholder="0.00"
                            class="w-full dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                            wire:model="state.amountFrom"
                            wire:keydown.enter="performSearch"
                        />

                        <span>-</span>

                        <input
                            type="number"
                            placeholder="0.00"
                            class="w-full dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                            wire:model="state.amountTo"
                            wire:keydown.enter="performSearch"
                        />
                    </div>
                </x-general.search.advanced-option>

                <x-general.search.advanced-option :title="trans('forms.search.fee_range')">
                    <div class="flex items-center space-x-2">
                        <input
                            type="number"
                            placeholder="0.00"
                            class="w-full dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                            wire:model="state.feeFrom"
                            wire:keydown.enter="performSearch"
                        />

                        <span>-</span>

                        <input
                            type="number"
                            placeholder="0.00"
                            class="w-full dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                            wire:model="state.feeTo"
                            wire:keydown.enter="performSearch"
                        />
                    </div>
                </x-general.search.advanced-option>

                <x-general.search.advanced-option :title="trans('forms.search.date_range')">
                    <div>
                        <input
                            type="date"
                            class="bg-transparent -ml-7"
                            wire:model="state.dateFrom"
                            style="width: 49px;"
                        />

                        <span>-</span>

                        <input
                            type="date"
                            class="-ml-6 bg-transparent"
                            wire:model="state.dateTo"
                            style="width: 49px;"
                        />
                    </div>
                </x-general.search.advanced-option>
            </div>

            <div class="flex items-center p-8 space-x-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800">
                <div class="flex flex-col flex-1 space-y-2">
                    <div class="text-sm font-semibold">@lang('forms.search.smartbridge')</div>

                    <input
                        type="text"
                        placeholder="@lang('forms.search.smartbridge_placeholder')"
                        class="w-full dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                        wire:model="state.smartBridge"
                        wire:keydown.enter="performSearch"
                    />
                </div>
            </div>
        </div>

        <div
            class="py-4 font-semibold text-center bg-theme-primary-100 text-theme-primary-600 md:hidden"
            @click="showAdvanced = !showAdvanced"
        >
            <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
            <span x-show="showAdvanced">@lang('actions.hide_search')</span>
        </div>
    </div>
</div>
