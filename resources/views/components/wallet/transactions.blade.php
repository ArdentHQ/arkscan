<x-ark-container
    class="border-t-4 border-theme-secondary-200 dark:border-black md:border-0"
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
        <livewire:wallet-tables :wallet="$wallet" />
    </div>
</x-ark-container>
