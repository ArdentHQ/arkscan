<div x-data="{
    filterOpen: false,
    transactionTypeFilter: '{{ $type }}',
    transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $type)',
}" x-cloak>
    <x-ark-dropdown
        wrapper-class="transaction-filter-wrapper"
        dropdown-classes="transaction-filter-dropdown"
        button-class="transaction-filter-button"
        dropdown-property="filterOpen"
        :init-alpine="false"
    >
        @slot('button')
            <div class="space-x-2 transaction-filter-button-container">
                <div>
                    <span class="text-theme-secondary-500 dark:text-theme-secondary-600">@lang('general.transaction.type'):</span>

                    <span
                        x-text="transactionTypeFilterLabel"
                        class="whitespace-no-wrap text-theme-secondary-900 md:text-theme-secondary-700 dark:text-theme-secondary-200"
                    ></span>
                </div>

                <span
                    :class="{ 'rotate-180 md:bg-theme-primary-600 md:text-theme-secondary-100': filterOpen }"
                    class="transaction-filter-button-icon"
                >
                    <x-ark-icon name="chevron-down" size="xs" class="md:h-3 md:w-2" />
                </span>
            </div>
        @endslot

        <div class="items-center justify-center block h-64 py-3 overflow-y-scroll dropdown-scrolling md:h-72">
            <div
                class="cursor-pointer dropdown-entry text-theme-secondary-900 dark:text-theme-secondary-200"
                @click="window.livewire.emit('filterTransactionsByType', 'all'); transactionTypeFilter = 'all'; transactionTypeFilterLabel = '@lang('forms.search.transaction_types.all')'"
            >
                @lang('forms.search.transaction_types.all')
            </div>

            <hr class="mx-8 mt-4 border-b border-dashed border-theme-secondary-300">

            @foreach([
                'core' => [
                    'transfer',
                    'secondSignature',
                    'delegateRegistration',
                    'vote',
                    'voteCombination',
                    'multiSignature',
                    'ipfs',
                    'multiPayment',
                    'delegateResignation',
                    'timelock',
                    'timelockClaim',
                    'timelockRefund',
                ],
                'magistrate' => [
                    'businessEntityRegistration',
                    'businessEntityResignation',
                    'businessEntityUpdate',
                    'delegateEntityRegistration',
                    'delegateEntityResignation',
                    'delegateEntityUpdate',
                    'entityRegistration',
                    'entityResignation',
                    'entityUpdate',
                    'legacyBridgechainRegistration',
                    'legacyBridgechainResignation',
                    'legacyBridgechainUpdate',
                    'legacyBusinessRegistration',
                    'legacyBusinessResignation',
                    'legacyBusinessUpdate',
                    'moduleEntityRegistration',
                    'moduleEntityResignation',
                    'moduleEntityUpdate',
                    'pluginEntityRegistration',
                    'pluginEntityResignation',
                    'pluginEntityUpdate',
                    'productEntityRegistration',
                    'productEntityResignation',
                    'productEntityUpdate',
                ],
            ] as $typeGroup => $types)
                <span class="flex items-center w-full px-8 pt-8 text-sm font-bold leading-5 text-left text-theme-secondary-500">{{ strtoupper($typeGroup) }}</span>

                @foreach ($types as $type)
                    <div
                        class="cursor-pointer dropdown-entry text-theme-secondary-900 dark:text-theme-secondary-200"
                        :class="{
                            'dropdown-entry-selected': transactionTypeFilter === '{{ $type }}'
                        }"
                        @click="window.livewire.emit('filterTransactionsByType', '{{ $type }}'); transactionTypeFilter = '{{ $type }}'; transactionTypeFilterLabel = '@lang('forms.search.transaction_types.'.$type)'"
                    >
                        @lang('forms.search.transaction_types.'.$type)
                    </div>
                @endforeach

                @if (! $loop->last)
                    <hr class="mx-8 mt-3 border-b border-dashed border-theme-secondary-300">
                @endif
            @endforeach
        </div>
    </x-ark-dropdown>
</div>
