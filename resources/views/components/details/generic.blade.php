<div class="flex items-center justify-between pb-4 border-b border-theme-secondary-300">
    <div class="flex flex-col space-y-2">
        <span class="text-sm font-semibold text-theme-secondary-500">{{ $title }}</span>
        @if((string) $slot === "")
            <span class="font-semibold text-theme-secondary-500">@lang('generic.not_specified')</span>
        @else
            <span class="text-lg font-semibold text-theme-secondary-700">{{ $slot }}</span>
        @endif
    </div>

    <div class="flex items-center justify-center w-12 h-12 p-2">
        @svg($icon, 'h-5 w-5 text-theme-secondary-900')
    </div>
</div>
