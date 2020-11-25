<x-general.search.advanced-option :title="trans('forms.search.type')">
    <x-ark-rich-select
        button-class="block w-full font-medium text-left bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200"
        initial-value="wallet"
        dispatch-event="search-type-changed"
        wire:model.defer="state.type"
        :options="[
            'block' => __('forms.search.block'),
            'transaction' => __('forms.search.transaction'),
            'wallet' => __('forms.search.wallet'),
        ]"
    />
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.balance_range')">
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

<x-general.search.advanced-option :title="trans('forms.search.username')">
    <input
        type="text"
        placeholder="@lang('forms.search.username')"
        class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
        wire:model.defer="state.username"
        wire:key="state_username"
        wire:keydown.enter="performSearch"
    />
</x-general.search.advanced-option>

<x-general.search.advanced-option :title="trans('forms.search.vote')">
    <input
        type="text"
        placeholder="@lang('forms.search.vote')"
        class="w-full dark:text-theme-secondary-200 dark:bg-theme-secondary-900"
        wire:model.defer="state.vote"
        wire:key="state_vote"
        wire:keydown.enter="performSearch"
    />
</x-general.search.advanced-option>
