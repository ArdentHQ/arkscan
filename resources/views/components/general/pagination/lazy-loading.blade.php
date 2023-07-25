@if ($this->isReady && ! $this->isOnLastPage())
    <div
        wire:poll.100ms.visible="nextPage"
        wire:loading.remove
    ></div>

    <div
        class="justify-center items-center mt-3 md:hidden"
        wire:loading.flex
    >
        <x-ark-loader-icon
            class="w-8 h-8"
            path-class="fill-theme-primary-600 dark:fill-theme-dark-blue-400"
            circle-class="stroke-theme-primary-100 dark:stroke-theme-dark-700"
        />
    </div>
@endif
