@props(['transaction'])

<div class="flex items-center space-x-2 text-xs isolate">
    <div class="text-theme-secondary-500 dark:text-theme-dark-200">
        @lang('general.search.to')
    </div>

    <x-general.identity
        :model="$transaction->recipient()"
        without-link
        class="text-theme-secondary-700 dark:text-theme-dark-50"
    />
</div>
