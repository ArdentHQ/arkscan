<div class="flex items-center py-6 px-8">
    <div class="flex-1 mr-8">
        <input
            x-ref="input"
            type="text"
            placeholder="@lang('forms.search.term_placeholder')"
            class="hidden w-full text-theme-secondary-700 dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700 overflow-ellipsis sm:block"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
        />
        <input
            x-ref="inputMobile"
            type="text"
            placeholder="@lang('forms.search.term_placeholder_mobile')"
            class="w-full dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700 overflow-ellipsis sm:hidden"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
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
        class="hidden button-primary md:block"
        wire:click="performSearch"
    >
        @lang('actions.find_it')
    </button>

    <div
        class="cursor-pointer text-theme-secondary-700 hover:text-theme-primary-600 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400 md:hidden"
        wire:click="performSearch"
    >
        <x-ark-icon name="search" />
    </div>
</div>
