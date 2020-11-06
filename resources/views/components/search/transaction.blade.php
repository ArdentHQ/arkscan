<x-general.search.advanced-option :title="trans('forms.search.type')">
    <select x-model="searchType" wire:model.defer="state.type" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200">
        <option value="block">@lang('forms.search.block')</option>
        <option value="transaction">@lang('forms.search.transaction')</option>
        <option value="wallet">@lang('forms.search.wallet')</option>
    </select>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.transaction_type')">
    <select wire:model.defer="state.transactionType" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200">
        @foreach(trans('forms.search.transaction_types') as $key => $value)
            <option value="{{ $key }}">{{ $value }}</option>
        @endforeach
    </select>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.amount_range')">
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

<x-general.search.advanced-option :title="trans('forms.search.fee_range')">
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

<x-general.search.advanced-option :title="trans('forms.search.date_range')">
    <div class="flex items-center space-x-2">
        <x-date-picker
            placeholder="DD/MM/YYYY"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.dateFrom"
            wire:key="state_date_from"
        />

        <span>-</span>

        <x-date-picker
            placeholder="DD/MM/YYYY"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.dateTo"
            wire:key="state_date_to"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.smartbridge')">
    <input
        type="text"
        placeholder="@lang('forms.search.smartbridge_placeholder')"
        class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900 smartbridge-placeholder"
        wire:model.defer="state.smartBridge"
        wire:keydown.enter="performSearch"
    />
</x-general.search.advanced-option>
