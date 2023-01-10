<x-rich-select
    wrapper-class="relative p-2 w-full rounded-xl border lg:p-0 lg:w-auto lg:border-0 border-theme-primary-100 dark:border-theme-secondary-800"
    dropdown-class="right-0 mt-2 origin-top-right"
    button-class="flex relative items-center p-3 mr-10 w-full font-semibold text-left lg:inline lg:items-end lg:px-8 focus:outline-none text-theme-secondary-900 dark:text-theme-secondary-200"
    icon-class="hidden"
    initial-value="{{ $this->state['type'] }}"
    wire:model="state.type"
    :options="Forms::getTransactionOptions()"
    width="w-auto"
>
    <x-slot name="dropdownEntry">
        <div class="flex justify-between items-center w-full font-semibold lg:justify-end lg:space-x-2 text-theme-secondary-500 lg:text-theme-secondary-700">
            <div>
                <span class="text-theme-secondary-500 dark:text-theme-secondary-600">@lang('general.transaction.type'):</span>

                <span
                    x-text="text"
                    class="whitespace-nowrap text-theme-secondary-900 lg:text-theme-secondary-700 dark:text-theme-secondary-200"
                ></span>
            </div>

            <span
                :class="{ 'rotate-180 lg:bg-theme-primary-600 lg:text-theme-secondary-100': open }"
                class="flex absolute right-0 justify-center items-center mr-4 w-6 h-6 rounded-full transition duration-150 ease-in-out lg:relative lg:mr-0 lg:w-4 lg:h-4 text-theme-secondary-400 lg:bg-theme-primary-100 lg:text-theme-primary-600 dark:bg-theme-secondary-800 dark:text-theme-secondary-200"
            >
                <x-ark-icon name="arrows.chevron-down-small" size="xs" class="lg:w-2 lg:h-3" />
            </span>
        </div>
    </x-slot>
</x-rich-select>
