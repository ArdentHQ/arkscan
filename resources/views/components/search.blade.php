{{-- TODO: Tidy up fields - review compared to design to see if they can be improved --}}
<div
    x-data="{
        showAdvanced: {{ $isAdvanced ? 'true' : 'false' }},
        isMobileOpen: false,
        isFocused: false,
        searchType: '{{ $type ?? 'block' }}',
    }"
    @mobile-search.window="isMobileOpen = true"
    class="searchbar @if ($isSlim ?? false) searchbar-slim @else shadow-lg rounded-b-lg @endif"
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

    <div class="search-container ">
        <div class="search-simple">
            @if ($isSlim ?? false)
                <div
                    x-show="isFocused"
                    class="mr-4 cursor-pointer text-theme-primary-600 hover:text-theme-primary-700"
                    @click="showAdvanced = false; isFocused = false; $dispatch('search-slim-close')"
                >
                    <x-icon name="close" size="md" />
                </div>
            @endif

            <div class="flex-1 mr-8">
                <input
                    type="text"
                    placeholder="@lang('forms.search.term_placeholder')"
                    class="hidden w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 {{ ($isSlim ?? false) ? 'xl:block' : 'sm:block' }}"
                    wire:model.defer="state.term"
                    wire:keydown.enter="performSearch"
                    @if ($isSlim ?? false) x-on:focus="isFocused = true; $dispatch('search-slim-expand')" @endif
                />

                <input
                    type="text"
                    placeholder="@lang('forms.search.term_placeholder_mobile')"
                    class="w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 {{ ($isSlim ?? false) ? 'xl:hidden' : 'sm:hidden' }}"
                    wire:model.defer="state.term"
                    wire:keydown.enter="performSearch"
                />
            </div>

            <button
                type="button"
                class="hidden text-theme-secondary-900 mr-8 rounded text-center transition-default font-normal hover:bg-theme-primary-100 dark:hover:bg-theme-secondary-800 dark:text-theme-secondary-600 md:block {{ ($isSlim ?? false) ? 'px-2 py-1 -my-2' : 'px-4 py-2' }}"
                @click="showAdvanced = !showAdvanced; isFocused = true; $dispatch('search-slim-expand')"
            >
                <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
                <span x-show="showAdvanced" x-cloak>@lang('actions.hide_search')</span>
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
                    <x-icon name="search" />
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
            x-cloak
        >
            <div class="search-advanced-options">
                <x-general.search.advanced-option :title="trans('forms.search.type')">
                    <select x-model="searchType" wire:model.defer="state.type" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200">
                        <option value="block">@lang('forms.search.block')</option>
                        <option value="transaction">@lang('forms.search.transaction')</option>
                        <option value="wallet">@lang('forms.search.wallet')</option>
                    </select>
                </x-general.search.advanced-option>

                <template x-if="searchType === 'block'">
                    <x-general.search.advanced-option :title="trans('forms.search.height_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.heightFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.heightTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'block'">
                    <x-general.search.advanced-option :title="trans('forms.search.total_amount_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.totalAmountFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.totalAmountTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'block'">
                    <x-general.search.advanced-option :title="trans('forms.search.total_fee_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.totalFeeFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.totalFeeTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'block'">
                    <x-general.search.advanced-option :title="trans('forms.search.reward_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.rewardFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.rewardTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'block'">
                    <x-general.search.advanced-option :title="trans('forms.search.date_range')">
                        <div>
                            <input
                                type="date"
                                class="bg-transparent -ml-7"
                                wire:model.defer="state.dateFrom"
                                style="width: 49px;"
                            />

                            <span>-</span>

                            <input
                                type="date"
                                class="-ml-6 bg-transparent"
                                wire:model.defer="state.dateTo"
                                style="width: 49px;"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'transaction'">
                    <x-general.search.advanced-option :title="trans('forms.search.transaction_type')">
                        <select wire:model.defer="state.transactionType" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200">
                            @foreach(trans('forms.search.transaction_types') as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'transaction'">
                    <x-general.search.advanced-option :title="trans('forms.search.amount_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.amountFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.amountTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'transaction'">
                    <x-general.search.advanced-option :title="trans('forms.search.fee_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.feeFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.feeTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'transaction'">
                    <x-general.search.advanced-option :title="trans('forms.search.smartbridge')">
                        <input
                            type="text"
                            placeholder="@lang('forms.search.smartbridge_placeholder')"
                            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                            wire:model.defer="state.smartBridge"
                            wire:keydown.enter="performSearch"
                        />
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'transaction'">
                    <x-general.search.advanced-option :title="trans('forms.search.date_range')">
                        <div>
                            <input
                                type="date"
                                class="bg-transparent -ml-7"
                                wire:model.defer="state.dateFrom"
                                style="width: 49px;"
                            />

                            <span>-</span>

                            <input
                                type="date"
                                class="-ml-6 bg-transparent"
                                wire:model.defer="state.dateTo"
                                style="width: 49px;"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'wallet'">
                    <x-general.search.advanced-option :title="trans('forms.search.balance_range')">
                        <div class="flex items-center space-x-2">
                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.balanceFrom"
                                wire:keydown.enter="performSearch"
                            />

                            <span>-</span>

                            <input
                                type="number"
                                placeholder="0.00"
                                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                                wire:model.defer="state.balanceTo"
                                wire:keydown.enter="performSearch"
                            />
                        </div>
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'wallet'">
                    <x-general.search.advanced-option :title="trans('forms.search.username')">
                        <input
                            type="text"
                            placeholder="@lang('forms.search.username')"
                            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                            wire:model.defer="state.username"
                            wire:keydown.enter="performSearch"
                        />
                    </x-general.search.advanced-option>
                </template>

                <template x-if="searchType === 'wallet'">
                    <x-general.search.advanced-option :title="trans('forms.search.vote')">
                        <input
                            type="text"
                            placeholder="@lang('forms.search.vote')"
                            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                            wire:model.defer="state.vote"
                            wire:keydown.enter="performSearch"
                        />
                    </x-general.search.advanced-option>
                </template>
            </div>
        </div>

        <div
            class="py-4 font-semibold text-center bg-theme-primary-100 text-theme-primary-600 dark:bg-theme-secondary-800 dark:text-theme-secondary-200 md:hidden"
            @click="showAdvanced = !showAdvanced"
        >
            <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
            <span x-show="showAdvanced">@lang('actions.hide_search')</span>
        </div>
    </div>
</div>
