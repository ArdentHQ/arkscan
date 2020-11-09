<livewire:wallet-qr-code :address="$wallet->address()" />

<button @click="livewire.emit('toggleQrCode')" type="button" class="flex items-center justify-center flex-1 w-16 px-3 rounded cursor-pointer bg-theme-primary-600 hover:bg-theme-primary-700 transition-default lg:flex-none h-11">
    <x-ark-icon name="app-qr-code" size="md" />
</button>
