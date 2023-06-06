@props([
    'title',
    'maskedMessage' => null,
])

<div class="flex flex-col flex-1 p-6 space-y-3 border-t-4 md:p-0 md:border-0 border-theme-secondary-200 dark:border-theme-secondary-800">
    <div class="font-semibold dark:text-theme-secondary-500">
        {{ $title }}
    </div>

    <div class="flex relative flex-col flex-1 space-y-3 md:p-6 md:rounded-xl md:border border-theme-secondary-300 dark:border-theme-secondary-800">
        {{ $slot }}

        @if ($maskedMessage)
            <div class="flex absolute inset-0 justify-center items-center text-sm font-semibold select-none backdrop-blur text-theme-secondary-500">
                {{ $maskedMessage }}
            </div>
        @endif
    </div>
</div>
