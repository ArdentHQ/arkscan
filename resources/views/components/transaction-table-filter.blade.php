<div
    x-data="{
        filterOpen: false,
        transactionTypeFilter: '{{ $type }}',
        transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $type)',
    }"
>
    <x-ark-dropdown
        wrapper-class="relative p-2 w-full rounded-xl border border-theme-primary-100 dark:border-theme-secondary-800 md:w-auto md:p-0 md:border-0"
        dropdown-classes="right-0 w-full mt-3 dark:bg-theme-secondary-900 md:w-84"
        button-class="flex items-center p-3 w-full font-semibold text-left focus:outline-none md:px-8 md:py-0 text-theme-secondary-900 dark:text-theme-secondary-200 md:items-end md:inline"
        dropdown-property="filterOpen"
        :init-alpine="false"
    >

        @slot('button')
            <div class="flex justify-between items-center space-x-2 w-full font-semibold text-theme-secondary-500 md:justify-end md:text-theme-secondary-700">
                <div>
                    <span class="text-theme-secondary-500 dark:text-theme-secondary-600">@lang('general.transaction.type'):</span>

                    <span
                        x-text="transactionTypeFilterLabel"
                        class="whitespace-nowrap text-theme-secondary-900 md:text-theme-secondary-700 dark:text-theme-secondary-200"
                    ></span>
                </div>

                <span
                    :class="{ 'rotate-180 md:bg-theme-primary-600 md:text-theme-secondary-100': filterOpen }"
                    class="flex justify-center items-center w-6 h-6 rounded-full transition duration-150 ease-in-out text-theme-secondary-400 dark:bg-theme-secondary-800 dark:text-theme-secondary-200 md:w-4 md:h-4 md:bg-theme-primary-100 md:text-theme-primary-600"
                >
                    <x-ark-icon name="chevron-down" size="xs" class="md:h-3 md:w-2" />
                </span>
            </div>
        @endslot

        <div class="block overflow-y-scroll justify-center items-center py-3 h-64 dropdown-scrolling md:h-72">
            @foreach([
                'all',
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
                'magistrate',
            ] as $type)
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
        </div>
    </x-ark-dropdown>
</div>
