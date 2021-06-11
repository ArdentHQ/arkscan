<div class="flex flex-1 font-semibold border-theme-secondary-800 lg:border-l lg:ml-8 lg:pl-8">
    <div class="hidden items-center md:flex">
        <div class="circled-icon text-theme-secondary-700 border-theme-secondary-800 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
            <x-ark-icon :name="$icon" />
        </div>
    </div>

    <div class="flex flex-col flex-1 justify-center space-y-2 md:pl-4">
        <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700 {{ $titleClass ?? '' }}">
            {{ $title }}
        </div>

        <div class="flex items-center space-x-2 leading-tight">
            <span class="truncate text-theme-secondary-400 dark:text-theme-secondary-200">
                {{ $slot }}
            </span>
        </div>
    </div>
</div>
