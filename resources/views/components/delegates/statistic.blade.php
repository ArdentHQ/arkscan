<div class="flex flex-row">
    <div class="flex items-center">
        <div class="circled-icon text-theme-secondary-400 border-theme-danger-400">
            {{ $logo }}
        </div>
    </div>

    <div class="flex flex-col flex-1 justify-between ml-4 font-semibold">
        <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">
            {{ $title }}
        </div>

        <div class="flex items-center space-x-2 leading-tight">
            <span class="truncate text-theme-secondary-400 dark:text-theme-secondary-200">
                {{ $slot }}
            </span>
        </div>
    </div>
</div>
