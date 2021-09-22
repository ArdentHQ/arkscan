<div class="flex items-center py-6 px-8">
    <div class="flex-1 mr-8">
        <input
            x-ref="input"
            type="text"
            placeholder="@lang('forms.search.term_placeholder')"
            class="hidden w-full sm:block text-theme-secondary-700 overflow-ellipsis dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
            @keydown.enter="searching = true"
        />
        <input
            x-ref="inputMobile"
            type="text"
            placeholder="@lang('forms.search.term_placeholder_mobile')"
            class="w-full sm:hidden overflow-ellipsis dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
            @keydown.enter="searching = true"
        />
    </div>

    <button
        type="button"
        class="hidden py-2 px-4 mr-8 font-normal text-center rounded md:block text-theme-primary-500 transition-default dark:hover:bg-theme-secondary-800 dark:text-theme-secondary-600 hover:bg-theme-primary-100"
        @click="showAdvanced = !showAdvanced;"
    >
        <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
        <span x-show="showAdvanced" x-cloak>@lang('actions.hide_advanced')</span>
    </button>

    <button
        type="button"
        class="hidden relative md:block button-primary"
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
        class="cursor-pointer md:hidden text-theme-secondary-700 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400 hover:text-theme-primary-600"
        wire:click="performSearch"
        @click="searching = true"
    >
        <span x-show="searching"><x-ark-spinner-icon /></span>
        <span x-show="!searching"><x-ark-icon name="search" /></span>
    </button>
</div>
