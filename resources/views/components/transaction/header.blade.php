<div class="dark:bg-theme-secondary-900">
    <div class="flex-col pt-16 space-y-6 content-container">
        <x-general.search.header-slim :title="trans('pages.transaction.title')" />

        <x-general.entity-header
            :title="trans('pages.transaction.transaction_id')"
            :value="$transaction->id()"
        >
            <x-slot name="logo"><span class="text-lg font-medium">ID</span></x-slot>

            <x-slot name="bottom">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-4">
                    <x-general.entity-header-item
                        :title="trans('pages.transaction.transaction_type')"
                        icon="app-transactions.transfer"
                        :text="$transaction->typeLabel()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.transaction.fee')"
                        icon="app-fee"
                        :text="$transaction->fee()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.transaction.amount')"
                        icon="app-votes"
                        :text="$transaction->amount()"
                    />
                    <x-general.entity-header-item
                        :title="trans('pages.transaction.confirmations')"
                        icon="app-confirmations"
                        :text="$transaction->confirmations()"
                    />
                </div>
            </x-slot>
        </x-general.entity-header>
    </div>
</div>
