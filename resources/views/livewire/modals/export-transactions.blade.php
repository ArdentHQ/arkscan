<div x-data="{
    dateRange: 'current_month',
    delimiter: 'comma',
    includeHeaderRow: false,
    types: [],
    columns: [],
}">
    <div
        class="flex-1 sm:flex-none"
        data-tippy-content="@lang('general.coming_soon')"
    >
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
            title-class="text-lg text-left font-semibold dark:text-theme-secondary-200 mb-6"
            padding-class="p-6"
            wire-close="cancel"
            close-button-class="absolute top-0 right-0 p-0 mt-0 mr-0 w-8 h-8 rounded-none sm:mt-6 sm:mr-6 sm:rounded button button-secondary text-theme-secondary-700 bg-transparent"
            buttons-style="flex flex-col sm:flex-row sm:justify-end !mt-6 sm:space-x-3 space-y-3 sm:space-y-0"
            breakpoint="sm"
            wrapper-class="max-w-full sm:max-w-[448px]"
            content-class="relative bg-white sm:mx-auto sm:shadow-2xl sm:rounded-xl dark:bg-theme-secondary-900"
        >
            <x-slot name="title">
                <div>@lang('pages.wallet.export-transactions-modal.title')</div>

                <div class="font-normal text-sm text-theme-secondary-700 mt-1">
                    @lang('pages.wallet.export-transactions-modal.description')
                </div>
            </x-slot>

            <x-slot name="description">
                <div class="flex flex-col pt-6 space-y-5 -mx-6 px-6 border-t border-theme-secondary-300">
                    <x-input.select
                        id="dateRange"
                        :label="trans('pages.wallet.export-transactions-modal.date_range')"
                        dropdown-width="w-[400px]"
                        :items="trans('pages.wallet.export-transactions-modal.date-options')"
                    />

                    <div class="flex flex-col space-y-3">
                        <x-input.select
                            id="delimiter"
                            :label="trans('pages.wallet.export-transactions-modal.delimiter')"
                            dropdown-width="w-[400px]"
                            :items="trans('pages.wallet.export-transactions-modal.delimiter-options')"
                        />

                        <x-ark-checkbox
                            name="include_header_row"
                            alpine="(e) => {console.log(e); includeHeaderRow = e.target.checked}"
                            :label="trans('pages.wallet.export-transactions-modal.include_header_row')"
                            :label-classes="Arr::toCssClasses([
                                'text-base',
                                // 'text-theme-secondary-900 dark:text-theme-secondary-200' => ! $isSelected,
                                // 'text-theme-primary-600 font-semibold dark:group-hover:text-theme-dark-blue-600 transition-default' => $isSelected,
                            ])"
                            class=""
                            wrapper-class="flex-1"
                            no-livewire
                        />
                    </div>

                    <x-input.select
                        id="types"
                        :label="trans('pages.wallet.export-transactions-modal.types')"
                        dropdown-width="w-[400px]"
                        :items="trans('pages.wallet.export-transactions-modal.types-options')"
                        :placeholder="trans('pages.wallet.export-transactions-modal.types_placeholder')"
                        multiple
                    />

                    <x-input.select
                        id="columns"
                        :label="trans('pages.wallet.export-transactions-modal.columns')"
                        dropdown-width="w-[400px]"
                        items="pages.wallet.export-transactions-modal.columns-options"
                        :item-lang-properties="[
                            'networkCurrency' => Network::currency(),
                            'userCurrency' => Settings::currency(),
                        ]"
                        :placeholder="trans('pages.wallet.export-transactions-modal.columns_placeholder')"
                        multiple
                    />
                </div>
            </x-slot>

            <x-slot name="buttons">
                <button
                    type="button"
                    class="button-secondary"
                    wire:click="cancel"
                >
                    @lang('actions.cancel')
                </button>

                <button
                    type="button"
                    class="button-primary"
                    @if ($this->canSubmit)
                        disabled
                    @endif
                >
                    @lang('actions.export')
                </button>
            </x-slot>
        </x-ark-modal>
    @endif
</div>
