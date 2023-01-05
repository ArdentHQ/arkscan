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
        <span class="flex animate-move-bg-start-right bg-gradient-to-r from-theme-danger-400 to-theme-danger-400 bg-500 bg-clip-text text-transparent dark:via-theme-hint-400 via-theme-hint-600">
            <span>
                @lang('menus.migration')
            </span>
        </span>

        <div class="absolute -right-3 flex h-6">
            <div
                class="inline-block animate-move-bg bg-gradient-to-r from-theme-danger-400 to-theme-danger-400 bg-500 dark:via-theme-hint-400 via-theme-hint-600"
                style="clip-path: url(#sparksClipPath)"
            >
                <x-ark-icon name="app-sparks" />
            </div>
        </div>
    </span>
</a>
