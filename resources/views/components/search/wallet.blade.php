<x-general.search.advanced-option :title="trans('forms.search.balance_range')" class="md:border-b" option-class="xl:border-r" type="wallet">
    <div class="flex items-center space-x-2">
        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.balanceFrom"
            wire:key="state_balance_from"
            wire:keydown.enter="performSearch"
        />

        <span>-</span>

        <input
            type="number"
            min="0"
            placeholder="0.00"
            class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
            wire:model.defer="state.balanceTo"
            wire:key="state_balance_to"
            wire:keydown.enter="performSearch"
        />
    </div>
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.username')" class="xl:border-b" option-class="md:border-r xl:border-r-0" type="wallet">
    <input
        type="text"
        placeholder="@lang('forms.search.username')"
        class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
        wire:model.defer="state.username"
        wire:key="state_username"
        wire:keydown.enter="performSearch"
    />
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.vote')" class="xl:border-b-0" option-class="lg:border-r-0 xl:border-r" type="wallet">
    <input
        type="text"
        placeholder="@lang('forms.search.vote')"
        class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
        wire:model.defer="state.vote"
        wire:key="state_vote"
        wire:keydown.enter="performSearch"
    />
</x-general.search.advanced-option>
