<div x-data="{
    filterOpen: false,
    transactionTypeFilter: 'all',
    transactionTypeFilterLabel: 'All',
}" x-cloak>
    <x-ark-dropdown
        dropdown-classes="left-0 w-64 mt-3 dark:bg-theme-secondary-900 w-full"
        button-class="items-end w-64 h-10 pb-0 pr-0 dropdown-button"
        dropdown-property="filterOpen"
        :init-alpine="false"
    >
        @slot('button')
            <div class="flex items-center justify-end w-full space-x-2 font-semibold flex-inline">
                <div>
                    <span class="text-theme-secondary-500">@lang('general.transaction.type'): </span>
                    <span class="text-theme-secondary-700"x-text="transactionTypeFilterLabel"></span>
                </div>

                <span
                    :class="{ 'rotate-180 bg-theme-primary-600 text-theme-secondary-100': filterOpen }"
                    class="flex items-center justify-center w-4 h-4 transition duration-150 ease-in-out rounded-full bg-theme-primary-100 dark:bg-theme-secondary-800 text-theme-primary-600 dark:text-theme-secondary-200"
                >
                    @svg('chevron-down', 'h-3 w-2')
                </span>
            </div>
        @endslot

        <div class="items-center justify-center block py-3 overflow-y-scroll h-128 dropdown-scrolling">
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
                    'delegateResignation',
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
