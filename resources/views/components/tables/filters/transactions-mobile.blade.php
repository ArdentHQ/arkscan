<div
    wire:init="setIsReady"
    class="flex-1 sm:h-8 md:hidden"
>
    <div>
        <button
            type="button"
            class="flex justify-center items-center space-x-2 w-full sm:py-1.5 sm:px-4 button-secondary"
            wire:click="openModal"
        >
            <x-ark-icon
                name="filter"
                size="sm"
            />

            <div class="ml-2 md:hidden">
                @lang('actions.filter')
            </div>
        </button>
    </div>

    @if($this->modalShown)
        <div>
            <x-ark-modal
                title-class="mb-3 text-lg font-semibold text-left sm:mb-4 dark:text-theme-dark-50"
                padding-class="p-6 py-4 sm:py-6"
                wire-close="closeModal"
                close-button-class="absolute top-0 right-0 p-0 mt-3 mr-6 w-8 h-8 bg-transparent rounded-none sm:mt-5 sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
                breakpoint="sm"
                wrapper-class="max-w-full sm:max-w-[448px]"
                content-class="relative bg-white sm:mx-auto sm:rounded-xl sm:shadow-2xl dark:bg-theme-secondary-900"
                disable-scroll-lock-at-width="768"
                disable-overlay-close
            >
                <x-slot name="title">
                    <div>@lang('pages.wallet.filter-transactions-modal.title')</div>
                </x-slot>

                <x-slot name="description">
                    <div class="pt-4 -mx-6 border-t border-theme-secondary-300 dark:border-theme-dark-700">
                        <x-tables.filters.includes.transaction-options />
                    </div>
                </x-slot>
            </x-ark-modal>
        </div>
    @endif
</div>
