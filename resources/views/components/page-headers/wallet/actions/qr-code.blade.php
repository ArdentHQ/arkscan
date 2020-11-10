<livewire:wallet-qr-code :address="$wallet->address()" />

<button
    @click="livewire.emit('toggleQrCode')"
    type="button"
    class="flex items-center justify-center flex-1 w-16 rounded cursor-pointer bg-theme-primary-600 hover:bg-theme-primary-700 transition-default h-11 lg:flex-none lg:px-3"
>
    <x-ark-icon name="app-qr-code" size="md" />
</button>
