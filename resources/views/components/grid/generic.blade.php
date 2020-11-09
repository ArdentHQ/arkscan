<div class="flex items-center justify-between pb-4">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">{{ $title }}</span>
        @if((string) $slot === "")
            <span class="font-semibold text-theme-secondary-500 dark:text-theme-secondary-200">@lang('generic.not_specified')</span>
        @else
            <span class="font-semibold text-theme-secondary-700 dark:text-theme-secondary-200">{{ $slot }}</span>
        @endif
    </div>

    <div class="flex items-center justify-center w-12 h-12">
        <div class="circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
            <x-ark-icon :name="$icon" class="text-theme-secondary-900 dark:text-theme-secondary-600" />
        </div>
    </div>
</div>
