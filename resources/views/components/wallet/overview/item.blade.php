@props([
    'title',
    'titleExtra' => null,
    'maskedMessage' => null,
])

<div {{ $attributes->class('flex flex-col flex-1 p-6 space-y-3 border-t-4 md:p-0 md:border-0 dark:border-black border-theme-secondary-200') }}>
    <div class="flex justify-between font-semibold dark:text-theme-dark-200">
        {{ $title }}

        @if ($titleExtra)
            {{ $titleExtra }}
        @endif
    </div>

    <div class="relative flex-1 md:rounded-xl md:border border-theme-secondary-300 dark:border-theme-dark-800">
        <div class="flex relative flex-col space-y-3 md:p-6">
            {{ $slot }}
        </div>

        @if ($maskedMessage)
            <div class="flex absolute inset-0 justify-center items-center -my-2 -mx-6 text-sm font-semibold select-none md:m-0 md:rounded-xl backdrop-blur text-theme-secondary-500">
                {{ $maskedMessage }}
            </div>
        @endif
    </div>
</div>
