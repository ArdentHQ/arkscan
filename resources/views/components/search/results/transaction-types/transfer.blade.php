@props(['transaction'])

<div class="flex items-center space-x-2 text-xs">
    <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
        @lang('general.search.to')
    </div>

    <x-general.identity
        :model="$transaction->recipient()"
        without-reverse
        without-reverse-class="space-x-2"
        without-link
        without-icon
        class="text-theme-secondary-700 dark:text-theme-secondary-500"
    />
</div>
