<div class="flex overflow-hidden relative flex-shrink-0 items-center h-8 rounded border-2 group border-theme-secondary-300 dark:border-theme-dark-800 group-hover:dark:border-theme-primary-600 focus-within:border-theme-primary-600 focus-within:dark:border-theme-primary-600 hover:border-theme-primary-600">

    <div class="flex items-center pr-2 pl-4">
        <x-ark-icon
            name="magnifying-glass-small"
            size="sm"
            class="text-theme-secondary-500 dark:text-theme-dark-600 dim:text-theme-dark-200"
        />
    </div>

    <div class="flex-1 h-full leading-none">
        <input
            x-ref="input"
            type="text"
            class="block py-2 w-full h-full text-theme-secondary-900 overflow-ellipsis dark:bg-theme-dark-900 dark:text-theme-dark-200 dim:text-theme-dark-50"
            wire:model.debounce.500ms="query"
            wire:keydown.enter="goToFirstResult"
            placeholder="@lang('general.navbar.search_placeholder')"
        />
    </div>

    <button
        type="button"
        @click="function () {
            $wire.clear();
            $refs.input.focus();
        }"
        class="pr-4 -my-px bg-transparent button-secondary text-theme-secondary-700 dark:bg-theme-dark-900 dark:text-theme-dark-600 dim:bg-transparent dim:shadow-none dim:text-theme-dark-50"
        x-show="query"
        x-cloak
    >
        <x-ark-icon
            name="cross"
            size="xs"
        />
    </button>

</div>
