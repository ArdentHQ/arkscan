@props ([
    'breakpoint' => 'lg', // Indicate at what breakpoint will styling switch from mobile to desktop UI
])

@php
    $wrapperClass = [
        'md' => 'md:p-0 md:w-auto md:border-0',
        'lg' => 'lg:p-0 lg:w-auto lg:border-0',
    ][$breakpoint];

    $buttonClass = [
        'md' => 'md:inline md:items-end md:px-8',
        'lg' => 'lg:inline lg:items-end lg:px-8',
    ][$breakpoint];

    $innerWrapperClass = [
        'md' => 'md:text-theme-secondary-700 md:justify-end md:space-x-2',
        'lg' => 'lg:text-theme-secondary-700 lg:justify-end lg:space-x-2',
    ][$breakpoint];

    $openIconClass = [
        'md' => 'md:bg-theme-primary-600 md:text-theme-secondary-100',
        'lg' => 'lg:bg-theme-primary-600 lg:text-theme-secondary-100',
    ][$breakpoint];

    $iconWrapperClass = [
        'md' => 'md:w-4 md:h-4 md:bg-theme-primary-100 md:text-theme-primary-600',
        'lg' => 'lg:w-4 lg:h-4 lg:bg-theme-primary-100 lg:text-theme-primary-600',
    ][$breakpoint];
@endphp

<x-rich-select
    wrapper-class="relative p-2 w-full rounded-xl border border-theme-primary-100 dark:border-theme-secondary-800 {{ $wrapperClass }}"
    dropdown-class="right-0 mt-2 origin-top-right"
    button-class="flex relative items-center p-3 mr-10 w-full font-semibold text-left {{ $buttonClass }} focus:outline-none text-theme-secondary-900 dark:text-theme-secondary-200"
    icon-class="hidden"
    initial-value="{{ $this->state['type'] }}"
    wire:model="state.type"
    :options="Forms::getTransactionOptions()"
    width="w-auto"
>
    <x-slot name="dropdownEntry">
        <div class="flex justify-between items-center w-full font-semibold text-theme-secondary-500 {{ $innerWrapperClass }}">
            <div>
                <span class="text-theme-secondary-500 dark:text-theme-secondary-600">@lang('general.transaction.type'):</span>

                <span
                    x-text="text"
                    @class(['whitespace-nowrap text-theme-secondary-900  dark:text-theme-secondary-200', [
                        'md' => 'md:text-theme-secondary-700',
                        'lg' => 'lg:text-theme-secondary-700',
                    ][$breakpoint]])
                ></span>
            </div>

            <span
                :class="{ 'rotate-180 {{ $openIconClass }}': open }"
                class="flex absolute right-0 justify-center items-center mr-4 w-6 h-6 rounded-full transition duration-150 ease-in-out text-theme-secondary-400 md:relative md:mr-0 {{ $iconWrapperClass }} dark:bg-theme-secondary-800 dark:text-theme-secondary-200"
            >
                <x-ark-icon name="arrows.chevron-down-small" size="xs" :class="[
                    'md' => 'md:w-2 md:h-3',
                    'lg' => 'lg:w-2 lg:h-3',
                ][$breakpoint]" />
            </span>
        </div>
    </x-slot>
</x-rich-select>
