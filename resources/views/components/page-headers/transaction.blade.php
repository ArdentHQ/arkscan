@props(['transaction'])

<x-page-headers.container
    :label="trans('pages.transaction.transaction_id')"
    breakpoint="sm"
>
    <div class="min-w-0">
        <x-truncate-dynamic>{{ $transaction->id() }}</x-truncate-dynamic>
    </div>

    <x-slot name="extra">
        <x-ark-clipboard
            :value="$transaction->id()"
            class="flex items-center p-2 w-full h-auto focus-visible:ring-inset group"
            wrapper-class="flex-1"
            :tooltip-content="trans('pages.transaction.transaction_id_copied')"
            with-checkmarks
            checkmarks-class="group-hover:text-white text-theme-primary-900 dark:text-theme-secondary-200"
        >
            <div class="ml-2 sm:hidden">
                @lang('actions.copy')
            </div>
        </x-ark-clipboard>
    </x-slot>
</x-page-headers.container>
