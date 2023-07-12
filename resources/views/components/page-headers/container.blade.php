@props([
    'label',
    'extra' => null,
])

<div
    x-data="{}"
    class="flex flex-col px-6 pt-8 pb-6 md:px-10 md:mx-auto md:max-w-7xl"
>
    <div class="flex overflow-hidden flex-col space-y-4 font-semibold sm:flex-row sm:justify-between sm:items-end sm:space-y-0 md:items-center md:rounded-lg md:border md:border-theme-secondary-300 md:dark:border-theme-secondary-800">
        <div class="flex flex-col space-y-2 min-w-0 md:flex-row md:items-center md:space-y-0 md:space-x-3">
            <div class="md:px-4 md:py-[14.5px] md:bg-theme-secondary-200 md:dark:bg-black text-sm md:text-lg dark:text-theme-secondary-500 !leading-[17px] md:!leading-[21px] whitespace-nowrap">
                {{ $label }}
            </div>

            <div class="min-w-0 leading-5 text-theme-secondary-900 md:leading-[21px] dark:text-theme-secondary-200">
                {{ $slot }}
            </div>
        </div>

        @if ($extra)
            <div class="flex space-x-2 w-full sm:w-auto md:px-3">
                {{ $extra }}
            </div>
        @endif
    </div>
</div>
