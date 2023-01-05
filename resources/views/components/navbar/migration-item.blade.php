@php ($currentRoute = optional(Route::current())->getName())

<a
    href="{{ route('migration') }}"
    @class([
        'inline-flex font-semibold leading-5 group focus:outline-none transition duration-150 ease-in-out h-full px-2 mx-2 relative border-t-2 border-transparent rounded',
        'text-theme-secondary-900 dark:text-theme-secondary-400' => $currentRoute === 'migration',
        'text-theme-secondary-700 hover:text-theme-secondary-800 dark:text-theme-secondary-500 dark:hover:text-theme-secondary-400' => $currentRoute !== 'migration',
    ])
>
    <span @class([
        'flex items-center w-full h-full border-b-2',
        'border-theme-primary-600' => $currentRoute === 'migration',
        'border-transparent group-hover:border-theme-secondary-300' => $currentRoute !== 'migration',
    ])>
        <span class="flex text-transparent bg-clip-text bg-gradient-to-r animate-move-bg-start-right from-theme-danger-400 to-theme-danger-400 bg-500 via-theme-hint-600 dark:via-theme-hint-400">
            <span>
                @lang('menus.migration')
            </span>
        </span>

        <div class="flex absolute -right-3 h-6">
            <div
                class="inline-block bg-gradient-to-r animate-move-bg from-theme-danger-400 to-theme-danger-400 bg-500 via-theme-hint-600 dark:via-theme-hint-400"
                style="clip-path: url(#sparksClipPath)"
            >
                <x-ark-icon name="app-sparks" />
            </div>
        </div>
    </span>
</a>
