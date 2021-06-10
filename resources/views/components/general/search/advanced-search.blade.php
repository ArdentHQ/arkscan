@props([
    'transactionOptions',
    'state',
    'type' => 'block',
])

<div
    x-ref="advancedSearch"
    x-transition:enter="transition ease-out duration-100"
    x-transition:enter-start="opacity-0 transform"
    x-transition:enter-end="opacity-100 transform"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100 transform"
    x-transition:leave-end="opacity-0 transform"
    {{ $attributes->merge(['class' => 'border-t border-theme-secondary-300 dark:border-theme-secondary-800']) }}
>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3">
        <x-general.search.advanced-option class="md:border-b" option-class="md:border-r" :title="trans('forms.search.type')">
            <x-ark-rich-select
                button-class="block w-full font-medium text-left bg-transparent text-theme-secondary-900 dark:text-theme-secondary-200"
                initial-value="{{ $type }}"
                dispatch-event="search-type-changed"
                wire:model.defer="state.type"
                :options="[
                    'block' => __('forms.search.block'),
                    'transaction' => __('forms.search.transaction'),
                    'wallet' => __('forms.search.wallet'),
                ]"
            />
        </x-general.search.advanced-option>

        <x-search.block />
        <x-search.transaction :transaction-options="$transactionOptions" :transaction-type="Arr::get($state, 'transactionType', 'all')" />
        <x-search.wallet />
    </div>
</div>
