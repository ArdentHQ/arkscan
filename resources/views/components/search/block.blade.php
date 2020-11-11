<x-general.search.advanced-option :title="trans('forms.search.type')">
    <x-ark-rich-select
        button-class="block w-full font-medium text-left bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200"
        initial-value="block"
        dispatch-event="search-type-changed"
        set-value-from-event="search-type-changed"
        wire:model.defer="state.type"
        :options="[
            'block' => __('forms.search.block'),
            'transaction' => __('forms.search.transaction'),
            'wallet' => __('forms.search.wallet'),
        ]"
    />
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.height_range')">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.heightFrom"
            wire:key="state_height_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.heightTo"
            wire:key="state_height_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.total_amount_range')">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.totalAmountFrom"
            wire:key="state_total_amount_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.totalAmountTo"
            wire:key="state_total_amount_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.total_fee_range')">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.totalFeeFrom"
            wire:key="state_total_fee_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.totalFeeTo"
            wire:key="state_total_fee_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.reward_range')">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.rewardFrom"
            wire:key="state_reward_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.rewardTo"
            wire:key="state_reward_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.date_range')">
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
