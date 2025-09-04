@props([
    'title',
    'description',
    'buttons',
])

<x-ark-modal
    :title="$title"
    title-class="text-lg font-semibold text-left mb-[0.875rem] dark:text-theme-dark-50"
    padding-class="py-4 px-6 sm:pb-4 sm:pt-[0.875rem]"
    wire-close="closeModal"
    close-button-class="absolute top-0 right-0 p-0 mt-4 mr-6 w-6 h-6 bg-transparent rounded-none sm:rounded dark:bg-transparent dark:shadow-none button button-secondary text-theme-secondary-700 dim:bg-transparent dim:shadow-none sm:mt-[0.875rem] dark:text-theme-dark-200 hover:dark:text-theme-dark-50 hover:dark:bg-theme-dark-blue-600"
    buttons-style="flex flex-col-reverse sm:flex-row sm:justify-end !mt-4 sm:!mt-6 sm:space-x-3 border-t border-theme-secondary-300 dark:border-theme-dark-700 px-6 -mx-6 pt-4"
    breakpoint="sm"
    wrapper-class="max-w-full sm:max-w-[448px]"
    content-class="relative bg-white sm:mx-auto sm:rounded-xl sm:shadow-2xl dark:bg-theme-dark-900"
    overlay-class="dim:bg-theme-dark-950"
    disable-overlay-close
>
    <x-slot name="description">
        {{ $description }}
    </x-slot>

    <x-slot name="buttons">
        {{ $buttons }}
    </x-slot>
</x-ark-modal>
