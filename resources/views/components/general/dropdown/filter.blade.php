@props([
    'mobile' => false,
    'withoutText' => false,
])

<x-general.dropdown.dropdown
    :placement="$mobile ? 'bottom' : 'right-start'"
    :placement-fallbacks="['bottom', 'bottom-end', 'left-start']"
    :dropdown-class="Arr::toCssClasses([
        'px-6 w-full md:px-8 table-filter',
        'sm:max-w-[268px]' => $mobile,
        'md:w-[284px]' => ! $mobile,
    ])"
    :close-on-click="false"
    class=""
    :dropdown-wrapper-class="Arr::toCssClasses([
        'w-full',
        'md:hidden' => $mobile,
        'hidden md:block' => ! $mobile,
    ])"
    dropdown-background="bg-white dark:bg-theme-secondary-900 dark:border dark:border-theme-secondary-800"
    dropdown-padding="py-1"
    :button-class="Arr::toCssClasses([
        'flex flex-1 justify-center items-center rounded sm:flex-none button-secondary',
        'h-8 w-8 p-0' => $withoutText,
        'w-full sm:py-1.5 sm:px-4 md:p-2' => ! $withoutText,
    ])"
    active-button-class=""
    :button-wrapper-class="Arr::toCssClasses([
        'w-full h-5 md:h-4' => ! $withoutText,
    ])"
>
    <x-slot name="button">
        <div class="inline-flex items-center mx-auto whitespace-nowrap">
            <x-ark-icon
                name="filter"
                size="sm"
            />

            @unless ($withoutText)
                <div class="ml-2 md:hidden">
                    @lang('actions.filter')
                </div>
            @endunless
        </div>
    </x-slot>

    {{ $slot }}
</x-general.dropdown.dropdown>
