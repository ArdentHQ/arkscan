@props(['transaction'])

<div class="flex items-center space-x-2 text-xs isolate">
    <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
        @lang('general.search.to')
    </div>

    <x-general.identity
        :model="$transaction->recipient()"
        without-reverse
        without-reverse-class="space-x-2"
        without-link
        class="text-theme-secondary-700 dark:text-theme-secondary-500"
    >
        <x-slot name="icon">
            <x-general.avatar-small
                :identifier="$transaction->recipient()->address"
                size="w-4 h-4 md:w-5 md:h-5"
            />
        </x-slot>
    </x-general.identity>
</div>
