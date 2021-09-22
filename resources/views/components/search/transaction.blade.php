@props(['transactionOptions', 'transactionType' => 'all'])

<x-general.search.advanced-option :title="trans('forms.search.transaction_type')" class="md:border-b" option-class="xl:border-r" type="transaction">
    <x-ark-rich-select
        button-class="block w-full font-medium text-left bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200"
        :initial-value="$transactionType"
        wire:model.defer="state.transactionType"
        :options="$transactionOptions"
    />
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.amount_range')" class="md:border-b" option-class="md:border-r xl:border-r-0" type="transaction">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.amountFrom"
            wire:key="state_amount_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.amountTo"
            wire:key="state_amount_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.fee_range')" class="md:border-b xl:border-b-0" option-class="lg:border-r-0 xl:border-r" type="transaction">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.feeFrom"
            wire:key="state_fee_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.feeTo"
            wire:key="state_fee_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.date_range')" option-class="md:border-r" type="transaction">
    <div class="flex items-center space-x-2">
        <x-date-picker
            placeholder="DD.MM.YYYY"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.dateFrom"
            wire:key="state_date_from"
        />

        <span>-</span>

        <x-date-picker
            placeholder="DD.MM.YYYY"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.dateTo"
            wire:key="state_date_to"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.smartbridge')" type="transaction">
    <input
        type="text"
        placeholder="@lang('forms.search.smartbridge_placeholder')"
        class="w-full smartbridge-placeholder dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
        wire:model.defer="state.smartBridge"
        wire:keydown.enter="performSearch"
    />
</x-general.search.advanced-option>
