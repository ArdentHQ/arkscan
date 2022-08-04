<div class="flex items-center py-4 px-6 md:py-5">
    <div class="flex-1 mr-8 leading-none">
        <input
            x-ref="input"
            type="text"
            placeholder="@lang('forms.search.term_placeholder')"
            class="hidden w-full sm:block text-theme-secondary-700 overflow-ellipsis dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
        />
        <input
            x-ref="inputMobile"
            type="text"
            placeholder="@lang('forms.search.term_placeholder_mobile')"
            class="w-full sm:hidden overflow-ellipsis dark:text-theme-secondary-700 dark:bg-theme-secondary-900 dark:placeholder-text-theme-secondary-700"
            wire:model.defer="state.term"
            wire:keydown.enter="performSearch"
        />
    </div>

    <div class="flex">
        <button
            type="button"
            class="hidden py-2 px-4 mr-8 font-normal text-center rounded md:block text-theme-primary-500 transition-default dark:hover:bg-theme-secondary-800 dark:text-theme-secondary-600 hover:bg-theme-primary-100"
            wire:click="toggleAdvanced"
            @click="showAdvanced = !showAdvanced;"
        >
            <span x-show="!showAdvanced">@lang('actions.advanced_search')</span>
            <span x-show="showAdvanced" x-cloak>@lang('actions.hide_advanced')</span>
        </button>

        <button
            type="button"
            class="hidden relative md:block button-primary"
            wire:click="performSearch"
            wire:target="performSearch"
            wire:loading.class="pointer-events-none"
        >
            <span
                wire:loading.class="inline"
                wire:loading.class.remove="hidden"
                wire:target="performSearch"
                class="flex hidden absolute inset-0 justify-center items-center"
                x-cloak
            >
                <x-ark-spinner-icon />
            </span>

            <span
                wire:loading.class="invisible"
                wire:target="performSearch"
            >
                @lang('actions.find_it')
            </span>
        </button>

        <button
            type="button"
            class="md:hidden button-primary"
            wire:click="performSearch"
        >
            <span
                wire:loading
                wire:target="performSearch"
                x-cloak
            >
                <x-ark-spinner-icon size="w-4 h-4" />
            </span>

            <span
                wire:loading.remove
                wire:target="performSearch"
            >
                <x-ark-icon name="magnifying-glass" size="sm" />
            </span>
        </button>
    </div>
</div>
