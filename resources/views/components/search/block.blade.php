<x-general.search.advanced-option :title="trans('forms.search.height_range')" class="md:border-b" option-class="xl:border-r" type="block">
    <div class="flex items-center space-x-2">
        <div class="flex-1">
            <input
                type="number"
                min="0"
                placeholder="0.00"
                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                wire:model.defer="state.heightFrom"
                wire:key="state_height_from"
                wire:keydown.enter="performSearch"
            />
        </div>

        <span>-</span>

        <div class="flex-1">
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
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.total_amount_range')" class="md:border-b" option-class="md:border-r xl:border-r-0" type="block">
    <div class="flex items-center space-x-2">
        <div class="flex-1">
            <input
                type="number"
                min="0"
                placeholder="0.00"
                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                wire:model.defer="state.totalAmountFrom"
                wire:key="state_total_amount_from"
                wire:keydown.enter="performSearch"
            />
        </div>

        <span>-</span>

        <div class="flex-1">
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
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.total_fee_range')" class="md:border-b xl:border-b-0" option-class="lg:border-r-0 xl:border-r" type="block">
    <div class="flex items-center space-x-2">
        <div class="flex-1">
            <input
                type="number"
                min="0"
                placeholder="0.00"
                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                wire:model.defer="state.totalFeeFrom"
                wire:key="state_total_fee_from"
                wire:keydown.enter="performSearch"
            />
        </div>

        <span>-</span>

        <div class="flex-1">
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
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.reward_range')" option-class="md:border-r" type="block">
    <div class="flex items-center space-x-2">
        <div class="flex-1">
            <input
                type="number"
                min="0"
                placeholder="0.00"
                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                wire:model.defer="state.rewardFrom"
                wire:key="state_reward_from"
                wire:keydown.enter="performSearch"
            />
        </div>

        <span>-</span>

        <div class="flex-1">
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
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.date_range')" type="block">
    <div class="flex items-center space-x-2">
        <div class="flex-1">
            <x-date-picker
                placeholder="DD.MM.YYYY"
                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                wire:model.defer="state.dateFrom"
                wire:key="state_date_from"
            />
        </div>

        <span>-</span>

        <div class="flex-1">
            <x-date-picker
                placeholder="DD.MM.YYYY"
                class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
                wire:model.defer="state.dateTo"
                wire:key="state_date_to"
            />
        </div>
    </div>
</x-general.search.advanced-option>
