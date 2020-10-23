<div x-cloak class="w-full">
    <div class="relative flex items-center justify-between">
        <h2 class="text-3xl sm:text-4xl">@lang('pages.home.transactions_and_blocks')</h2>
        <div x-show="selected === 'transactions'">
            <x-ark-dropdown dropdown-classes="left-0 w-64 mt-3" button-class="w-64 h-10 dropdown-button" :init-alpine="false">
                @slot('button')
                    <div class="flex items-center justify-end w-full space-x-2 font-semibold flex-inline text-theme-secondary-700">
                        <div>
                            @lang('general.transaction.type'): <span x-text="transactionTypeFilterLabel"></span>
                        </div>
                        <span :class="{ 'rotate-180': open }" class="flex items-center justify-center w-4 h-4 transition duration-150 ease-in-out rounded-full bg-theme-primary-100">
                            @svg('chevron-up', 'h-3 w-2 text-theme-primary-600')
                        </span>
                    </div>
                @endslot

                <div class="py-3">
                    @foreach([
                        'all',
                        'businessEntityRegistration',
                        'businessEntityResignation',
                        'businessEntityUpdate',
                        'delegateEntityRegistration',
                        'delegateEntityResignation',
                        'delegateEntityUpdate',
                        'delegateRegistration',
                        'delegateResignation',
                        'entityRegistration',
                        'entityResignation',
                        'entityUpdate',
                        'ipfs',
                        'legacyBridgechainRegistration',
                        'legacyBridgechainResignation',
                        'legacyBridgechainUpdate',
                        'legacyBusinessRegistration',
                        'legacyBusinessResignation',
                        'legacyBusinessUpdate',
                        'moduleEntityRegistration',
                        'moduleEntityResignation',
                        'moduleEntityUpdate',
                        'multiPayment',
                        'multiSignature',
                        'pluginEntityRegistration',
                        'pluginEntityResignation',
                        'pluginEntityUpdate',
                        'productEntityRegistration',
                        'productEntityResignation',
                        'productEntityUpdate',
                        'secondSignature',
                        'timelockClaim',
                        'timelockRefund',
                        'timelock',
                        'transfer',
                        'vote',
                    ] as $type)
                    <div class="cursor-pointer dropdown-entry" @click="window.livewire.emit('filterTransactionsByType', '{{ $type }}'); transactionTypeFilter = '{{ $type }}'; transactionTypeFilterLabel = '@lang('forms.search.transaction_types.'.$type)'">
                        @lang('forms.search.transaction_types.'.$type)
                    </div>
                    @endforeach
                </div>
            </x-ark-dropdown>
        </div>
    </div>
</div>
