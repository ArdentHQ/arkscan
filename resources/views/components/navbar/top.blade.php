<div class="hidden md:flex md:flex-col sticky top-0 bg-white dark:bg-theme-secondary-900 z-30">
    <div class="flex w-full content-container items-center justify-between py-3">
        <div class="flex items-center">
            @if(Network::canBeExchanged())
                <div class="hidden font-semibold md:flex dark:text-white text-theme-secondary-900">
                    <livewire:price-ticker />
                </div>
            @endif
        </div>

        <div>
            <x-ark-input-with-prefix
                icon="magnifying-glass"
                type="text"
                id="search"
                name="search"
                placeholder="Address / Tx ID / Block ID"
                container-class="flex bg-theme-secondary-200 dark:bg-theme-secondary-800 pl-1"
                wrapper-class-override="relative rounded shadow-sm"
                field-class-override="block w-full border-0 rounded outline-none appearance-none transition-default pl-2 pr-4 py-2 text-sm leading-4 placeholder:text-theme-secondary-700 text-theme-secondary-900 dark:text-theme-secondary-400 bg-theme-secondary-200 dark:bg-theme-secondary-800"
                hide-label
                disable-dirty-styling
            />
        </div>
    </div>

    <div class="absolute bottom-0 border-b border-theme-secondary-300 dark:border-theme-secondary-800 w-full"></div>
</div>
