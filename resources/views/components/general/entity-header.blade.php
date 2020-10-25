<div class="flex flex-col overflow-hidden border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="p-8 bg-black dark:bg-theme-secondary-900">
        <div class="flex flex-col md:flex-row">
            <div class="items-center hidden md:flex">
                <div class="circled-icon text-theme-secondary-400 border-theme-danger-400">
                    {{ $logo }}
                </div>
            </div>

            <div class="flex flex-col justify-between flex-1 font-semibold md:ml-4">
                <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">{{ $title }}</div>

                <div class="flex items-center space-x-2 leading-tight">
                    <span class="truncate text-theme-secondary-400 dark:text-theme-secondary-200">{{ $value }}</span>

                    <x-ark-clipboard :value="$value" class="flex items-center w-auto h-auto text-theme-secondary-600" no-styling />
                </div>
            </div>

            @if ($extra ?? false)
                {{ $extra }}
            @endif
        </div>
    </div>

    @isset($bottom)
        <div class="p-8 border-t bg-theme-secondary-100 border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
            {{ $bottom }}
        </div>
    @endisset
</div>
