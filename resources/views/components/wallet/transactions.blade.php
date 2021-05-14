<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <x-ark-container>
        <div x-data="{
            dropdownOpen: false,
            direction: 'all',
            transactionTypeFilter: 'all',
            transactionTypeFilterLabel: 'All',
        }" x-cloak class="w-full">
            <div class="w-full md:mb-8">
                <div class="flex relative flex-col justify-between md:items-end md:flex-row md:justify-start">
                    <h4 class="mb-8 md:mb-0">@lang('pages.wallet.transaction_history')</h4>

                    <x-transaction-table-filter :type="'all'" />
                </div>
            </div>

            <livewire:wallet-transaction-table
                :address="$wallet->address()"
                :public-key="$wallet->publicKey()"
                :is-cold="$wallet->isCold()"
            />
        </div>
    </x-ark-container>
</div>
