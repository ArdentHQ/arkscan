{{-- TODO: Tidy up fields - review compared to design to see if they can be improved --}}
<div
    x-data="{
        showAdvanced: {{ $isAdvanced ? 'true' : 'false' }},
        isMobileOpen: false,
        isFocused: false,
    }"
    @mobile-search.window="isMobileOpen = true"
    class="searchbar @if ($isSlim ?? false) searchbar-slim @endif"
    x-bind:class="{
        'search-mobile': isMobileOpen,
        'search-advanced': showAdvanced,
        'search-focused': isFocused,
    }"
>
    <div
        class="fixed inset-0 z-30 overflow-y-auto opacity-75 bg-theme-secondary-900 md:hidden"
        @click="isMobileOpen = false"
    ></div>

    <div class="search-container">
        <div class="search-simple">
            @if ($isSlim ?? false)
                <div
                    x-show="isFocused"
                    class="mr-4 cursor-pointer text-theme-primary-600 hover:text-theme-primary-700"
                    @click="showAdvanced = false; isFocused = false; $dispatch('search-slim-close')"
                >
                    @svg('close', 'w-6 h-6')
                </div>
            @endif

            <div class="flex-1 mr-8">
                <input
                    type="text"
                    placeholder="@lang('forms.search.term_placeholder')"
                    class="hidden w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 {{ ($isSlim ?? false) ? 'xl:block' : 'sm:block' }}"
                    wire:model="state.term"
                    wire:keydown.enter="performSearch"
                    @if ($isSlim ?? false) x-on:focus="isFocused = true; $dispatch('search-slim-expand')" @endif
                />

                <input
                    type="text"
                    placeholder="@lang('forms.search.term_placeholder_mobile')"
                    class="w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 {{ ($isSlim ?? false) ? 'xl:hidden' : 'sm:hidden' }}"
                    wire:model="state.term"
                    wire:keydown.enter="performSearch"
                />
            </div>

            <button
                type="button"
                class="hidden text-theme-secondary-900 mr-8 rounded text-center transition-default font-normal hover:bg-theme-primary-100 dark:hover:bg-theme-secondary-800 dark:text-theme-secondary-600 md:block {{ ($isSlim ?? false) ? 'px-2 py-1 -my-2' : 'px-4 py-2' }}"
                @click="showAdvanced = !showAdvanced; isFocused = true; $dispatch('search-slim-expand')"
            >
                <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
                <span x-show="showAdvanced">@lang('actions.hide_search')</span>
            </button>

            @unless($isSlim ?? false)
                <button
                    type="button"
                    class="hidden button-primary md:block"
                    wire:click="performSearch"
                >
                    @lang('actions.find_it')
                </button>
            @else
                <div
                    class="cursor-pointer text-theme-primary-300 hover:text-theme-primary-400 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400 @unless($isSlim ?? false) md:hidden @endif"
                    wire:click="performSearch"
                >
                    @svg('search', 'h-5 w-5')
                </div>
            @endunless
        </div>

        <div
            x-show="showAdvanced"
            @unless($isSlim ?? false)
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 transform"
                x-transition:enter-end="opacity-100 transform"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 transform"
                x-transition:leave-end="opacity-0 transform"
            @endunless
        >
            <div class="search-advanced-options">
                <x-general.search.advanced-option :title="trans('forms.search.type')">
                    <select wire:model="state.type" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                        <option value="block">Block</option>
                        <option value="transaction">Transaction</option>
                        <option value="wallet">Wallet</option>
                    </select>
                </x-general.search.advanced-option>

                <x-general.search.advanced-option :title="trans('forms.search.transaction_type')">
                    {{-- TODO: Enum of types and their values? --}}
                    <select wire:model="state.transactionType" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-700">
                        <option value="businessEntityRegistration">@lang('forms.search.transaction_types.businessEntityRegistration')</option>
                        <option value="businessEntityResignation">@lang('forms.search.transaction_types.businessEntityResignation')</option>
                        <option value="businessEntityUpdate">@lang('forms.search.transaction_types.businessEntityUpdate')</option>
                        <option value="delegateEntityRegistration">@lang('forms.search.transaction_types.delegateEntityRegistration')</option>
                        <option value="delegateEntityResignation">@lang('forms.search.transaction_types.delegateEntityResignation')</option>
                        <option value="delegateEntityUpdate">@lang('forms.search.transaction_types.delegateEntityUpdate')</option>
                        <option value="delegateRegistration">@lang('forms.search.transaction_types.delegateRegistration')</option>
                        <option value="delegateResignation">@lang('forms.search.transaction_types.delegateResignation')</option>
                        <option value="entityRegistration">@lang('forms.search.transaction_types.entityRegistration')</option>
                        <option value="entityResignation">@lang('forms.search.transaction_types.entityResignation')</option>
                        <option value="entityUpdate">@lang('forms.search.transaction_types.entityUpdate')</option>
                        <option value="ipfs">@lang('forms.search.transaction_types.ipfs')</option>
                        <option value="legacyBridgechainRegistration">@lang('forms.search.transaction_types.legacyBridgechainRegistration')</option>
                        <option value="legacyBridgechainResignation">@lang('forms.search.transaction_types.legacyBridgechainResignation')</option>
                        <option value="legacyBridgechainUpdate">@lang('forms.search.transaction_types.legacyBridgechainUpdate')</option>
                        <option value="legacyBusinessRegistration">@lang('forms.search.transaction_types.legacyBusinessRegistration')</option>
                        <option value="legacyBusinessResignation">@lang('forms.search.transaction_types.legacyBusinessResignation')</option>
                        <option value="legacyBusinessUpdate">@lang('forms.search.transaction_types.legacyBusinessUpdate')</option>
                        <option value="moduleEntityRegistration">@lang('forms.search.transaction_types.moduleEntityRegistration')</option>
                        <option value="moduleEntityResignation">@lang('forms.search.transaction_types.moduleEntityResignation')</option>
                        <option value="moduleEntityUpdate">@lang('forms.search.transaction_types.moduleEntityUpdate')</option>
                        <option value="multiPayment">@lang('forms.search.transaction_types.multiPayment')</option>
                        <option value="multiSignature">@lang('forms.search.transaction_types.multiSignature')</option>
                        <option value="pluginEntityRegistration">@lang('forms.search.transaction_types.pluginEntityRegistration')</option>
                        <option value="pluginEntityResignation">@lang('forms.search.transaction_types.pluginEntityResignation')</option>
                        <option value="pluginEntityUpdate">@lang('forms.search.transaction_types.pluginEntityUpdate')</option>
                        <option value="productEntityRegistration">@lang('forms.search.transaction_types.productEntityRegistration')</option>
                        <option value="productEntityResignation">@lang('forms.search.transaction_types.productEntityResignation')</option>
                        <option value="productEntityUpdate">@lang('forms.search.transaction_types.productEntityUpdate')</option>
                        <option value="secondSignature">@lang('forms.search.transaction_types.secondSignature')</option>
                        <option value="timelockClaim">@lang('forms.search.transaction_types.timelockClaim')</option>
                        <option value="timelockRefund">@lang('forms.search.transaction_types.timelockRefund')</option>
                        <option value="timelock">@lang('forms.search.transaction_types.timelock')</option>
                        <option value="transfer">@lang('forms.search.transaction_types.transfer')</option>
                        <option value="vote">@lang('forms.search.transaction_types.vote')</option>
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

                <x-general.search.advanced-option :title="trans('forms.search.smartbridge')">
                    <input
                        type="text"
                        placeholder="@lang('forms.search.smartbridge_placeholder')"
                        class="w-full dark:text-theme-secondary-600 dark:bg-theme-secondary-900"
                        wire:model="state.smartBridge"
                        wire:keydown.enter="performSearch"
                    />
                </x-general.search.advanced-option>
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
