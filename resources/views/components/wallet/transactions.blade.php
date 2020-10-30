<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="py-16 content-container md:px-8">
        <div x-data="{
            dropdownOpen: false,
            direction: 'all',
            transactionTypeFilter: 'all',
            transactionTypeFilterLabel: 'All',
        }" x-cloak class="w-full">
            <div class="w-full md:mb-8">
                <div class="relative flex flex-col justify-between md:items-end md:flex-row md:justify-start">
                    <h2 class="mb-8 text-2xl md:mb-0">@lang('pages.wallet.transaction_history')</h2>

                    <x-transaction-table-filter />
                </div>
            </div>

            <livewire:wallet-transaction-table :address="$wallet->address()" :public-key="$wallet->publicKey()" />
        </div>
    </div>
</div>
