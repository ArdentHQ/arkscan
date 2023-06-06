@props([
    'title',
    'maskedMessage' => null,
])

<div class="flex flex-col flex-1 space-y-3 border-t-4 border-theme-secondary-200 md:border-0 dark:border-theme-secondary-800 p-6 md:p-0">
    <div class="font-semibold dark:text-theme-secondary-500">
        {{ $title }}
    </div>

    <div class="flex flex-col flex-1 md:p-6 md:rounded-xl md:border border-theme-secondary-300 dark:border-theme-secondary-800 space-y-3 relative">
        {{ $slot }}

        @if ($maskedMessage)
            <div class="absolute flex items-center justify-center inset-0 backdrop-blur text-sm font-semibold text-theme-secondary-500 select-none">
                {{ $maskedMessage }}
            </div>
        @endif
    </div>
</div>
