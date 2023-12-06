@props([
    'title' => null,
])

<div {{ $attributes->class('px-6 md:px-10 md:mx-auto md:max-w-7xl group/stats last:mb-8 dark:text-theme-dark-200 mt-3 first:mt-2') }}>
    <div class="flex md:mt-0 md:space-x-3">
        <div class="hidden flex-col ml-3 md:flex w-[1.625rem]">
            <div @class([
                'hidden w-full border-l-2 md:block group-first/stats:md:block border-theme-secondary-300 h-[30px] group-first/stats:h-[18px] dark:border-theme-dark-700',
                'md:-mt-3 group-first/stats:md:-mt-0',
            ])></div>

            <div class="hidden w-full rounded-bl-xl border-b-2 border-l-2 md:block border-theme-secondary-300 min-h-[12px] dark:border-theme-dark-700"></div>

            <div class="hidden flex-1 w-full border-l-2 md:block group-last/stats:hidden border-theme-secondary-300 min-h-[12px] dark:border-theme-dark-700"></div>
        </div>

        <div class="flex flex-col flex-1 space-y-3 font-semibold md:pb-0 md:space-y-0 border border-theme-secondary-300 dark:border-theme-dark-700 rounded md:rounded-xl pb-4">
            @if ($title)
                <div class="md:hidden py-3 px-4 text-sm md:py-0 md:px-0 md:bg-transparent md:border-0 bg-theme-secondary-100 dark:md:bg-transparent dark:bg-theme-dark-950 rounded-t">
                    {{ $title }}
                </div>
            @endif

            <div class="flex flex-col px-4 text-sm md:text-base md:leading-5 md:px-6 md:py-4 md:space-y-3 md-lg:w-[476px]">
                <div class="hidden md:inline-flex">
                    <x-general.badge class="md:text-sm md:py-1 md:px-2">{{ $title }}</x-general.badge>
                </div>

                <div class="flex flex-col flex-1 whitespace-nowrap divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-dark-700 space-y-3 md:divide-none">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
