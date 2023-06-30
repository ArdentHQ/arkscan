<div class="flex flex-col space-y-5">
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
</div>
