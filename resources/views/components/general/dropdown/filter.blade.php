@props([
    'mobile' => false,
])

@if ($mobile)
    <div
        x-data="Modal.alpine({ shown: false })"
        class="md:hidden"
    >
        <x-ark-js-modal
            :init="false"
            padding="p-6 sm:p-8 sm:pb-6"
            title-class="text-lg sm:text-2xl"
            width-class="max-w-full sm:max-w-[430px]"
            breakpoint="sm"
            content-class="sm:rounded-2.5xl"
        >
            <x-slot name="title">
                @lang('general.filter')
            </x-slot>

            <x-slot name="description">
                <div class="-mx-8">
                    {{ $slot }}
                </div>
            </x-slot>
        </x-ark-js-modal>

        <button
            class="flex flex-1 justify-center items-center w-full rounded sm:flex-none sm:py-1.5 sm:px-4 md:p-2 button-secondary"
            @click="shown = true"
        >
            <div class="inline-flex items-center mx-auto whitespace-nowrap">
                <x-ark-icon
                    name="filter"
                    size="sm"
                />

                <div class="ml-2 md:hidden">
                    @lang('actions.filter')
                </div>
            </div>
        </button>
    </div>
@else
    <x-general.dropdown.dropdown
        placement="right-start"
        :placement-fallbacks="['bottom', 'bottom-end', 'left-start']"
        dropdown-class="px-6 w-full md:px-8 table-filter md:w-[284px]"
        :close-on-click="false"
        class=""
        :dropdown-wrapper-class="Arr::toCssClasses([
            'w-full',
            'hidden md:block' => ! $mobile,
        ])"
        dropdown-background="bg-white dark:bg-theme-secondary-900 dark:border dark:border-theme-secondary-800"
        dropdown-padding="py-1"
        button-class="flex flex-1 justify-center items-center w-full rounded sm:flex-none sm:py-1.5 sm:px-4 md:p-2 button-secondary"
        active-button-class=""
        button-wrapper-class="w-full h-5 md:h-4"
    >
        <x-slot name="button">
            <div class="inline-flex items-center mx-auto whitespace-nowrap">
                <x-ark-icon
                    name="filter"
                    size="sm"
                />

                <div class="ml-2 md:hidden">
                    @lang('actions.filter')
                </div>
            </div>
        </x-slot>

        {{ $slot }}
    </x-general.dropdown.dropdown>
@endif
