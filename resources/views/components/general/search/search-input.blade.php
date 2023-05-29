<div class="group relative rounded flex border-2 items-center border-theme-secondary-300 dark:border-theme-secondary-800 group-hover:dark:border-theme-primary-600 focus-within:border-theme-primary-600 focus-within:dark:border-theme-primary-600 hover:border-theme-primary-600 overflow-hidden h-8 flex-shrink-0">

    <div class="flex items-center pl-4 pr-2">
        <x-ark-icon
            name="magnifying-glass"
            size="sm"
            class="dark:text-theme-secondary-600"
        />
    </div>

    <div class="flex-1 leading-none h-full">
        <input
            x-ref="input"
            type="text"
            class="w-full block text-theme-secondary-900 overflow-ellipsis h-full py-2 dark:bg-theme-secondary-900 dark:text-theme-secondary-200"
            wire:model.debounce.500ms="query"
            wire:keydown.enter="performSearch"
            placeholder="@lang('general.navbar.search_placeholder')"
        />
    </div>

    <button
        type="button"
        wire:click="clear"
        class="pr-4 -my-px bg-transparent button-secondary text-theme-secondary-700 dark:bg-theme-secondary-900 dark:text-theme-secondary-600"
        x-show="query"
        x-cloak
    >
        <x-ark-icon
            name="cross"
            size="xs"
        />
    </button>

</div>
