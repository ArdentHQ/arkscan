@props(['address'])

<div
    x-data="{
        showOptions: false,
        hasBeenOpened: false,
        preventAmountScroll: (e) => {
            const hasFocus = document.activeElement === e.target;
            e.target.blur();
            e.stopPropagation();
            if (hasFocus) {
                setTimeout(() => {
                    e.target.focus()
                }, 0);
            }
        }
    }"
    class="flex-1"
>
    <x-general.dropdown.dropdown
        width="w-full sm:max-w-[320px] px-4"
        active-button-class=""
        dropdown-padding=""
        dropdown-wrapper-class="w-full"
        dropdown-background="bg-white border border-transparent dark:shadow-lg-dark dark:bg-theme-dark-900 dark:border-theme-dark-800"
        :close-on-click="false"
        on-close="() => showOptions = false"
        on-open="() => {
            if (! hasBeenOpened) {
                hasBeenOpened = true;

                sa_event('qr_code_opened');
            }
        }"
        button-wrapper-class=""
        button-class="p-2 w-full focus-visible:ring-inset button button-secondary button-icon"
    >
        <x-slot
            name="button"
            wire:click="toggleQrCode"
        >
            <div>
                <x-ark-icon name="qr-code" size="sm" />
            </div>
        </x-slot>

        <livewire:wallet-qr-code :address="$address" />
    </x-general.dropdown>
</div>
