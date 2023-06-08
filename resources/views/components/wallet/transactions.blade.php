<x-ark-container
    class="border-t-4 md:border-0 dark:border-black border-theme-secondary-200"
    container-class="md:pt-0"
>
    <div
        x-data="{
            dropdownOpen: false,
            direction: 'all',
        }"
        class="w-full"
        x-cloak
    >
        <livewire:wallet-tables
            :address="$wallet->address()"
            :public-key="$wallet->publicKey()"
            :is-cold="$wallet->isCold()"
        />
    </div>
</x-ark-container>
