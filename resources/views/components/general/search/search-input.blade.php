<div class="flex items-center py-6 px-8">
    <div class="flex-1 mr-8">
        <input
            x-ref="input"
            type="text"
            placeholder="@lang('forms.search.term_placeholder')"
            class="hidden w-full text-theme-secondary-700 dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700 overflow-ellipsis sm:block"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
            @keydown.enter="searching = true"
        />
        <input
            x-ref="inputMobile"
            type="text"
            placeholder="@lang('forms.search.term_placeholder_mobile')"
            class="w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700 overflow-ellipsis sm:hidden"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
            @keydown.enter="searching = true"
        />
    </div>

    <button
        type="button"
        class="hidden py-2 px-4 mr-8 font-normal text-center rounded text-theme-primary-500 transition-default hover:bg-theme-primary-100 dark:hover:bg-theme-secondary-800 dark:text-theme-secondary-600 md:block"
        @click="showAdvanced = !showAdvanced;"
    >
        <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
        <span x-show="showAdvanced" x-cloak>@lang('actions.hide_advanced')</span>
    </button>

    <button
        type="button"
        class="hidden relative button-primary md:block"
        :class="{ 'pointer-events-none' : searching }"
        wire:click="performSearch"
        @click="searching = true"
    >
        <span x-show="searching" class="flex absolute right-0 left-0 justify-center items-center" x-cloak>
            <x-ark-spinner-icon />
        </span>
        <span :class="{ 'invisible': searching }">@lang('actions.find_it')</span>
    </button>

    <button
        type="button"
        class="cursor-pointer text-theme-secondary-700 hover:text-theme-primary-600 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400 md:hidden"
        wire:click="performSearch"
        @click="searching = true"
    >
        <span x-show="searching"><x-ark-spinner-icon /></span>
        <span x-show="!searching"><x-ark-icon name="search" /></span>
    </button>
</div>
