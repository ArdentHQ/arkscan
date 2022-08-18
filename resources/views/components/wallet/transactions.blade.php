<x-ark-container class="border-t border-theme-secondary-300 dark:border-theme-secondary-800">
    <div x-data="{
        dropdownOpen: false,
        direction: 'all',
    }" x-cloak class="w-full">
        <livewire:wallet-transaction-table
            :address="$wallet->address()"
            :public-key="$wallet->publicKey()"
            :is-cold="$wallet->isCold()"
        />
    </div>
</x-ark-container>
