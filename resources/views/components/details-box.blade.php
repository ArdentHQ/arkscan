<div class="flex space-x-5 detail-box">
    @isset($icon)
        @isset($shallow)
            <div class="flex-shrink-0 circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                <x-ark-icon :name="$icon" />
            </div>
        @else
            <div class="flex items-center justify-center p-2 rounded-full h-12 w-12 flex-shrink-0 bg-theme-secondary-200 {{ $iconWrapperClass ?? '' }} dark:bg-theme-secondary-800">
                <x-ark-icon :name="$icon" :class="($iconTextClass ?? ' ') . ' ' . ($iconClass ?? '')" />
            </div>
        @endisset
    @endisset

    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">
            {{ $title }}
        </span>

        <div @if ($tooltip ?? false) data-tippy-content="{{ $tooltip }}" @endif>
            @if((string) $slot === "")
                <span class="font-semibold text-theme-secondary-700 dark:text-theme-secondary-200">@lang('generic.not_specified')</span>
            @else
                <span class="font-semibold text-theme-secondary-700 dark:text-theme-secondary-200">
                    {{ $slot }}
                </span>
            @endif
        </div>
    </div>
</div>
