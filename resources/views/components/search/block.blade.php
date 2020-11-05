<x-general.search.advanced-option :title="trans('forms.search.type')">
    <select x-model="searchType" wire:model.defer="state.type" class="w-full font-medium bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200">
        <option value="block">@lang('forms.search.block')</option>
        <option value="transaction">@lang('forms.search.transaction')</option>
        <option value="wallet">@lang('forms.search.wallet')</option>
    </select>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.height_range')">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.heightFrom"
            wire:key="state_height_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
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
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.totalAmountFrom"
            wire:key="state_total_amount_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
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
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.totalFeeFrom"
            wire:key="state_total_fee_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
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
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.rewardFrom"
            wire:key="state_reward_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.rewardTo"
            wire:key="state_reward_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.date_range')">
    <div>
        <input
            type="date"
            class="bg-transparent -ml-7"
            wire:model.defer="state.dateFrom"
            wire:key="state_date_from"
            style="width: 49px;"
        />

        <span>-</span>

        <input
            type="date"
            class="-ml-6 bg-transparent"
            wire:model.defer="state.dateTo"
            wire:key="state_date_to"
            style="width: 49px;"
        />
    </div>
</x-general.search.advanced-option>
