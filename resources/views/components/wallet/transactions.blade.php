<x-ark-container
    class="border-t-4 md:border-0 dark:border-black border-theme-secondary-200"
    container-class="pt-6 md:pt-0"
>
    <div
        x-data="{
            dropdownOpen: false,
            direction: 'all',
        }"
        class="w-full"
        x-cloak
    >
        <livewire:wallet-tables :wallet="$wallet" />
    </div>
</x-ark-container>
