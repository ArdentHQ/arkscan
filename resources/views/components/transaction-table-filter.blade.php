<div x-cloak>
    <x-ark-dropdown dropdown-classes="left-0 w-64 mt-3 overflow-y-scroll h-128" button-class="items-end w-64 h-10 pb-0 pr-0 dropdown-button" :init-alpine="false">
        @slot('button')
            <div class="flex items-center justify-end w-full space-x-2 font-semibold flex-inline text-theme-secondary-700">
                <div>
                    @lang('general.transaction.type'): <span x-text="transactionTypeFilterLabel"></span>
                </div>
                <span :class="{ 'rotate-180 bg-theme-primary-600 text-theme-secondary-100': dropdownOpen }" class="flex items-center justify-center w-4 h-4 transition duration-150 ease-in-out rounded-full bg-theme-primary-100 text-theme-primary-600">
                    @svg('chevron-up', 'h-3 w-2')
                </span>
            </div>
        @endslot

        <div class="items-center justify-center block py-3">
            <div class="cursor-pointer dropdown-entry text-theme-secondary-900" @click="window.livewire.emit('filterTransactionsByType', 'all'); transactionTypeFilter = 'all'; transactionTypeFilterLabel = '@lang('forms.search.transaction_types.all')'">
                @lang('forms.search.transaction_types.all')
            </div>

            <div class="w-full border-b border-theme-secondary-300"></div>

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
                <span class="flex items-center w-full px-8 pt-8 text-sm font-bold leading-5 text-left text-theme-secondary-500">{{ ucfirst($typeGroup) }}</span>

                @foreach ($types as $type)
                    <div
                        class="cursor-pointer dropdown-entry text-theme-secondary-900"
                        @click="window.livewire.emit('filterTransactionsByType', '{{ $type }}'); transactionTypeFilter = '{{ $type }}'; transactionTypeFilterLabel = '@lang('forms.search.transaction_types.'.$type)'"
                    >
                        @lang('forms.search.transaction_types.'.$type)
                    </div>
                @endforeach

                @if (! $loop->last)
                    <div class="w-full border-b border-theme-secondary-300"></div>
                @endif
            @endforeach
        </div>
    </x-ark-dropdown>
</div>
