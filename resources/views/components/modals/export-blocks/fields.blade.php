<div class="flex flex-col space-y-5">
    <x-input.js-select
        id="dateRange"
        :label="trans('pages.wallet.export-blocks-modal.date_range')"
        dropdown-width="w-full sm:w-[400px]"
        :items="trans('pages.wallet.export-blocks-modal.date-options')"
    />

    <div class="flex flex-col space-y-3">
        <x-input.js-select
            id="delimiter"
            :label="trans('pages.wallet.export-blocks-modal.delimiter')"
            dropdown-width="w-full sm:w-[400px]"
            :items="trans('pages.wallet.export-blocks-modal.delimiter-options')"
        />

        <x-ark-checkbox
            name="include_header_row"
            x-model="includeHeaderRow"
            :label="trans('pages.wallet.export-blocks-modal.include_header_row')"
            label-classes="text-sm transition-default"
            class="export-modal__checkbox"
            wrapper-class="flex-1"
            no-livewire
        />
    </div>

    <x-input.js-select
        id="columns"
        :label="trans('pages.wallet.export-blocks-modal.columns')"
        dropdown-width="w-full sm:w-[400px]"
        items="pages.wallet.export-blocks-modal.columns-options"
        :item-criteria="fn ($key) => Network::canBeExchanged() || ! in_array($key, ['volumeFiat', 'totalFiat', 'rate'])"
        :item-lang-properties="[
            'networkCurrency' => Network::currency(),
            'userCurrency' => Settings::currency(),
        ]"
        :placeholder="trans('pages.wallet.export-blocks-modal.columns_placeholder')"
        :selected-pluralized-langs="trans('pages.wallet.export-blocks-modal.columns_x_selected')"
        multiple
    />
</div>
