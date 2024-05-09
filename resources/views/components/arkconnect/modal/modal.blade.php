@props([
    'title',
    'description',
    'button',
])

<x-ark-js-modal
    :init="false"
    padding="p-6"
    title-class="text-lg font-semibold pr-10"
    width-class="max-w-full sm:max-w-[448px]"
    buttons-style="!mt-4 sm:!mt-6 sm:space-x-3 flex flex-col-reverse sm:flex-row justify-end"
    breakpoint="sm"
    content-class="sm:rounded-2.5xl"
    close-button-class="absolute top-0 right-0 p-0 mt-4 mr-6 w-8 h-8 bg-transparent rounded-none sm:mt-6 sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dim:bg-transparent dim:shadow-none dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
>
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    <x-slot name="description">
        <div {{ $description->attributes->class('flex items-center -mx-6 px-6 justify-center border-y border-theme-secondary-300 dark:border-theme-dark-700 my-4 py-4 sm:mt-6 sm:py-6') }}>
            {{ $description }}
        </div>
    </x-slot>

    <x-slot name="buttons">
        <button
            type="button"
            x-on:click="hide"
            class="mt-3 sm:mt-0 button-secondary"
        >
            @lang('actions.cancel')
        </button>

        {{ $button }}
    </x-slot>
</x-ark-js-modal>
