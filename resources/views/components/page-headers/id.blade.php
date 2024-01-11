@props([
    'model',
    'title',
    'copyTooltip',
])

<x-page-headers.container
    :label="$title"
    breakpoint="sm"
>
    <div class="min-w-0">
        <x-truncate-dynamic>{{ $model->id() }}</x-truncate-dynamic>
    </div>

    <x-slot name="extra">
        <x-ark-clipboard
            :value="$model->id()"
            class="flex items-center p-2 w-full h-8 focus-visible:ring-inset group dim:bg-theme-dark-700 dim:shadow-none"
            wrapper-class="flex-1"
            :tooltip-content="$copyTooltip"
            with-checkmarks
            checkmarks-class="group-hover:text-white text-theme-primary-900 dark:text-theme-dark-200"
        >
            <div class="ml-2 sm:hidden">
                @lang('actions.copy')
            </div>
        </x-ark-clipboard>
    </x-slot>
</x-page-headers.container>
