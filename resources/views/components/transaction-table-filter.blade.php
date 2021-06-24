<x-ark-rich-select
    wrapper-class="relative p-2 w-full rounded-xl border md:p-0 md:w-auto md:border-0 border-theme-primary-100 dark:border-theme-secondary-800"
    dropdown-class="right-0 mt-2 origin-top-right"
    button-class="flex relative items-center p-3 w-full font-semibold text-left md:inline md:items-end md:px-8 focus:outline-none text-theme-secondary-900 dark:text-theme-secondary-200"
    icon-class="hidden"
    initial-value="{{ $this->state['type'] }}"
    wire:model="state.type"
    :options="Forms::getTransactionOptions()"
>
    <x-slot name="dropdownEntry">
        <div class="flex justify-between items-center w-full font-semibold md:justify-end md:space-x-2 text-theme-secondary-500 md:text-theme-secondary-700">
            <div>
                <span class="text-theme-secondary-500 dark:text-theme-secondary-600">@lang('general.transaction.type'):</span>

                <span
                    x-text="text"
                    class="whitespace-nowrap text-theme-secondary-900 md:text-theme-secondary-700 dark:text-theme-secondary-200"
                ></span>
            </div>

            <span
                :class="{ 'rotate-180 md:bg-theme-primary-600 md:text-theme-secondary-100': open }"
                class="flex absolute right-0 justify-center items-center mr-4 w-6 h-6 rounded-full transition duration-150 ease-in-out md:relative md:mr-0 md:w-4 md:h-4 text-theme-secondary-400 md:bg-theme-primary-100 md:text-theme-primary-600 dark:bg-theme-secondary-800 dark:text-theme-secondary-200"
            >
                <x-ark-icon name="chevron-down" size="xs" class="md:w-2 md:h-3" />
            </span>
        </div>
    </x-slot>
</x-ark-rich-select>
