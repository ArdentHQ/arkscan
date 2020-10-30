<div class="flex justify-between flex-1 pl-4 font-semibold border-l md:ml-8 border-theme-secondary-800">
    <div class="items-center hidden md:flex">
        <div class="circled-icon text-theme-secondary-700 border-theme-secondary-800">
            @svg($icon, 'w-5 h-5')
        </div>
    </div>

    <div class="flex flex-col justify-between flex-1 pl-4">
        <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">
            @lang($title)
        </div>

        <div class="flex items-center space-x-2 leading-tight">
            <span class="truncate text-theme-secondary-400 dark:text-theme-secondary-200">
                {{ $slot }}
            </span>
        </div>
    </div>
</div>
