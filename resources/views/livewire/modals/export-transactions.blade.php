<div
    x-data="TransactionsExport({
        address: '{{ $this->address }}',
        network: {{ json_encode(Network::toArray()) }},
        userCurrency: '{{ Settings::currency() }}',
        rate: {{ ExchangeRate::currentRate() }},
    })"
    class="flex-1 h-8 export-modal"
>
    <div>
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
            padding-class="p-6 py-4 sm:py-6"
            wire-close="closeModal"
            close-button-class="absolute top-0 right-0 p-0 mt-4 mr-6 w-8 h-8 bg-transparent rounded-none sm:mt-6 sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
            buttons-style="flex flex-col-reverse sm:flex-row sm:justify-end !mt-4 sm:!mt-6 sm:space-x-3"
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
                <div class="px-6 pt-6 -mx-6 border-t border-theme-secondary-300 dark:border-theme-dark-700">
                    <div x-show="! hasStartedExport">
                        <x-modals.export-transactions.fields />
                    </div>

                    <div x-show="hasStartedExport">
                        <x-modals.export-transactions.export-status />
                    </div>
                </div>
            </x-slot>

            <x-slot name="buttons">
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
                        class="flex justify-center items-center space-x-2 sm:py-1.5 sm:px-4 sm:mb-0 button-primary"
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
            </x-slot>
        </x-ark-modal>
    @endif
</div>
