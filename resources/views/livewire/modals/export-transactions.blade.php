<div
<<<<<<< HEAD
    x-data="TransactionsExport({
        address: '{{ $this->address }}',
        network: {{ json_encode(Network::toArray()) }},
        userCurrency: '{{ Settings::currency() }}',
        rate: {{ ExchangeRate::currentRate() }},
    })"
    class="export-modal"
>
    <div
        class="flex-1 sm:flex-none"
        data-tippy-content="@lang('general.coming_soon')"
    >
=======
    x-data="{
        dateRange: 'current_month',
        delimiter: 'comma',
        includeHeaderRow: false,
        types: {{ json_encode(array_map(fn ($item) => false, trans('pages.wallet.export-transactions-modal.types-options'))) }},
        columns: {{ json_encode(array_map(fn ($item) => false, trans('pages.wallet.export-transactions-modal.columns-options'))) }},

        canExport() {
            if (Object.values(this.types).filter(enabled => enabled).length === 0) {
                return false;
            }

            return Object.values(this.columns).filter(enabled => enabled).length === 0;
        },
    }"
    class="flex-1 h-8 export-modal"
>
    <div data-tippy-content="@lang('general.coming_soon')">
>>>>>>> origin/transaction-export
        <button
            type="button"
            class="flex justify-center items-center space-x-2 w-full sm:py-1.5 sm:px-4 button-secondary"
            wire:click="openModal"
        >
            <x-ark-icon
                name="arrows.underline-arrow-down"
                size="sm"
            />

            <div>@lang('actions.export')</div>
        </button>
    </div>

    @if($this->modalShown)
        <x-ark-modal
            title-class="mb-6 text-lg font-semibold text-left dark:text-theme-dark-50"
<<<<<<< HEAD
            padding-class="p-6"
            wire-close="closeModal"
            close-button-class="absolute top-0 right-0 p-0 mt-0 mr-0 w-8 h-8 bg-transparent rounded-none sm:mt-6 sm:mr-6 sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
            buttons-style="flex flex-col sm:flex-row sm:justify-end !mt-6 sm:space-x-3 space-y-3 sm:space-y-0"
=======
            padding-class="p-6 py-4 sm:py-6"
            wire-close="closeModal"
            close-button-class="absolute top-0 right-0 p-0 mt-4 mr-6 w-8 h-8 bg-transparent rounded-none sm:mt-6 sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
            buttons-style="flex flex-col-reverse sm:flex-row sm:justify-end !mt-4 sm:!mt-6 sm:space-x-3"
>>>>>>> origin/transaction-export
            breakpoint="sm"
            wrapper-class="max-w-full sm:max-w-[448px]"
            content-class="relative bg-white sm:mx-auto sm:rounded-xl sm:shadow-2xl dark:bg-theme-secondary-900"
        >
            <x-slot name="title">
                <div>@lang('pages.wallet.export-transactions-modal.title')</div>

                <div class="mt-1 text-sm font-normal text-theme-secondary-700 dark:text-theme-dark-200">
                    @lang('pages.wallet.export-transactions-modal.description')
                </div>
            </x-slot>

            <x-slot name="description">
<<<<<<< HEAD
                <div class="px-6 pt-6 -mx-6 border-t border-theme-secondary-300 dark:border-theme-dark-700">
                    <div x-show="! hasStartedExport">
                        <x-modals.export-transactions.fields />
                    </div>

                    <div x-show="hasStartedExport">
                        <x-modals.export-transactions.export-status />
                    </div>
=======
                <div class="flex flex-col py-4 px-6 -mx-6 space-y-5 sm:py-6 border-y border-theme-secondary-300 dark:border-theme-dark-700">
                    <x-input.js-select
                        id="dateRange"
                        :label="trans('pages.wallet.export-transactions-modal.date_range')"
                        dropdown-width="w-full sm:w-[400px]"
                        :items="trans('pages.wallet.export-transactions-modal.date-options')"
                    />

                    <div class="flex flex-col space-y-3">
                        <x-input.js-select
                            id="delimiter"
                            :label="trans('pages.wallet.export-transactions-modal.delimiter')"
                            dropdown-width="w-full sm:w-[400px]"
                            :items="trans('pages.wallet.export-transactions-modal.delimiter-options')"
                        />

                        <x-ark-checkbox
                            name="include_header_row"
                            x-model="includeHeaderRow"
                            :label="trans('pages.wallet.export-transactions-modal.include_header_row')"
                            label-classes="text-base transition-default"
                            class="export-modal__checkbox"
                            wrapper-class="flex-1"
                            no-livewire
                        />
                    </div>

                    <x-input.js-select
                        id="types"
                        :label="trans('pages.wallet.export-transactions-modal.types')"
                        dropdown-width="w-full sm:w-[400px]"
                        :items="trans('pages.wallet.export-transactions-modal.types-options')"
                        :placeholder="trans('pages.wallet.export-transactions-modal.types_placeholder')"
                        :selected-pluralized-langs="trans('pages.wallet.export-transactions-modal.types_x_selected')"
                        multiple
                    />

                    <x-input.js-select
                        id="columns"
                        :label="trans('pages.wallet.export-transactions-modal.columns')"
                        dropdown-width="w-full sm:w-[400px]"
                        items="pages.wallet.export-transactions-modal.columns-options"
                        :item-lang-properties="[
                            'networkCurrency' => Network::currency(),
                            'userCurrency' => Settings::currency(),
                        ]"
                        :placeholder="trans('pages.wallet.export-transactions-modal.columns_placeholder')"
                        :selected-pluralized-langs="trans('pages.wallet.export-transactions-modal.columns_x_selected')"
                        multiple
                    />
>>>>>>> origin/transaction-export
                </div>
            </x-slot>

            <x-slot name="buttons">
<<<<<<< HEAD
                <div
                    x-show="! hasStartedExport"
                    class="flex modal-buttons"
                >
                    <button
                        type="button"
                        class="button-secondary"
                        wire:click="closeModal"
                    >
                        @lang('actions.cancel')
                    </button>

                    <button
                        type="button"
                        class="flex justify-center items-center space-x-2 sm:py-1.5 sm:px-4 button-primary"
                        x-bind:disabled="! canExport()"
                        x-on:click="exportTransactions"
                    >
                        <x-ark-icon
                            name="arrows.underline-arrow-down"
                            size="sm"
                        />

                        <span>@lang('actions.export')</span>
                    </button>
                </div>

                <div
                    x-show="hasStartedExport"
                    class="flex modal-buttons"
                >
                    <button
                        type="button"
                        class="button-secondary"
                        x-on:click="hasStartedExport = false"
                    >
                        @lang('actions.back')
                    </button>

                    <a
                        x-bind:href="dataUri"
                        class="flex items-center sm:py-0 sm:px-4 button-primary"
                        :class="{
                            disabled: dataUri === null
                        }"
                        x-bind:download="`${address}.csv`"
                    >
                        <div class="flex justify-center items-center space-x-2 h-full">
                            <x-ark-icon
                                name="arrows.underline-arrow-down"
                                size="sm"
                            />

                            <span>@lang('actions.download')</span>
                        </div>
                    </a>
                </div>
=======
                <button
                    type="button"
                    class="button-secondary"
                    wire:click="closeModal"
                >
                    @lang('actions.cancel')
                </button>

                <button
                    type="button"
                    class="flex justify-center items-center mb-3 space-x-2 sm:py-1.5 sm:px-4 sm:mb-0 button-primary"
                    x-bind:disabled="canExport"
                >
                    <x-ark-icon
                        name="arrows.underline-arrow-down"
                        size="sm"
                    />

                    <span>@lang('actions.export')</span>
                </button>
>>>>>>> origin/transaction-export
            </x-slot>
        </x-ark-modal>
    @endif
</div>
