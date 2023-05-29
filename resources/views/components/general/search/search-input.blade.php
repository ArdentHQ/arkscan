<div class="flex overflow-hidden relative flex-shrink-0 items-center h-8 rounded border-2 group border-theme-secondary-300 dark:border-theme-secondary-800 group-hover:dark:border-theme-secondary-700 focus-within:border-theme-primary-600 focus-within:dark:border-theme-secondary-700 hover:border-theme-primary-600">

    <div class="flex items-center pr-2 pl-4">
        <x-ark-icon
            name="magnifying-glass"
            size="sm"
        />
    </div>

    <div class="flex-1 h-full leading-none">
        <input
            x-ref="input"
            type="text"
            class="block py-2 w-full h-full text-theme-secondary-900 overflow-ellipsis dark:bg-theme-secondary-900"
            wire:model.debounce.500ms="query"
            wire:keydown.enter="performSearch"
            :placeholder="trans('general.navbar.search_placeholder')"
        />
    </div>

    <button
        type="button"
        wire:click="clear"
        class="pr-4 -my-px bg-transparent button-secondary text-theme-secondary-700"
        x-show="query"
        x-cloak
    >
        <x-ark-icon
            name="cross"
            size="xs"
        />
    </button>

</div>
