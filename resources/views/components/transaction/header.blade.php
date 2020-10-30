<div class="dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.transaction.title')" />

        <x-general.entity-header
            :title="trans('pages.transaction.transaction_id')"
            :value="$transaction->id()"
        >
            <x-slot name="logo">
                <x-headings.circle>
                    <span class="text-lg font-medium">ID</span>
                </x-headings.circle>
            </x-slot>

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.transaction.transaction_type')"
                        icon="app-transactions.transfer"
                        :text="$transaction->typeLabel()"
                    />
                    @if($transaction->isMultiSignature())
                        <x-general.entity-header-item
                            :title="trans('pages.transaction.musig_participants')"
                            icon="app-transactions-amount"
                        >
                            <x-slot name="text">
                                @lang('pages.transaction.musig_participants_text', [
                                    $transaction->multiSignatureMinimum(),
                                    $transaction->multiSignatureParticipantCount()
                                ])
                            </x-slot>
                        </x-general.amount-fiat-tooltip>
                    @else
                        <x-general.entity-header-item
                            :title="trans('pages.transaction.amount')"
                            icon="app-transactions-amount"
                        >
                            <x-slot name="text">
                                <x-currency>{{ $transaction->amount() }}</x-currency>
                            </x-slot>
                        </x-general.amount-fiat-tooltip>
                    @endif
                    <x-general.entity-header-item
                        :title="trans('pages.transaction.fee')"
                        icon="app-fee"
                    >
                        <x-slot name="text">
                            <x-currency>{{ $transaction->fee() }}</x-currency>
                        </x-slot>
                    </x-general.amount-fiat-tooltip>

                    <x-general.entity-header-item
                        :title="trans('pages.transaction.confirmations')"
                        icon="app-confirmations"
                    >
                        <x-slot name="text">
                            <x-number>{{ $transaction->confirmations() }}</x-number>
                        </x-slot>
                    </x-general.amount-fiat-tooltip>
                </div>
            </x-slot>
        </x-general.entity-header>
    </div>
</div>
