@props(['transaction'])

<div class="flex items-center space-x-2 text-xs">
    <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
        @lang('general.search.vote')
    </div>

    <x-general.identity
        :model="$transaction->voted()"
        without-reverse
        without-reverse-class="space-x-2"
        without-link
        class="text-theme-secondary-700 dark:text-theme-secondary-500"
    >
        <x-slot name="icon">
            <x-general.avatar-small
                :identifier="$transaction->voted()->address()"
                size="w-5 h-5"
            />
        </x-slot>
    </x-general.identity>
</div>
