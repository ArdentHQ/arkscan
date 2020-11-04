<div class="flex space-x-2 detail-box">
    @isset($icon)
        @isset($shallow)
            <div class="flex-shrink-0 circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                <x-icon :name="$icon" />
            </div>
        @else
            <div class="flex items-center justify-center p-2 rounded-full h-12 w-12 mr-3 flex-shrink-0 bg-theme-secondary-200 {{ $iconWrapperClass ?? '' }} dark:bg-theme-secondary-800">
                @svg($icon, "h-5 w-5 " . ($iconTextClass ?? " ") . ($iconClass ?? ""))
            </div>
        @endisset
    @endisset

    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">{{ $title }}</span>
        @if((string) $slot === "")
            <span class="font-semibold text-theme-secondary-700 dark:text-theme-secondary-200">@lang('generic.not_specified')</span>
        @else
            <span class="font-semibold text-theme-secondary-700 dark:text-theme-secondary-200">
                {{ $slot }}
            </span>
        @endif
    </div>
</div>
