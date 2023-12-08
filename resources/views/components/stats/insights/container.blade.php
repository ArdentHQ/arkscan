@props([
    'title' => null,
    'fullWidth' => false,
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

        <div class="flex flex-col flex-1 pb-4 space-y-3 font-semibold rounded border md:pb-0 md:space-y-0 md:rounded-xl border-theme-secondary-300 dark:border-theme-dark-700">
            @if ($title)
                <div class="py-3 px-4 text-sm rounded-t md:hidden md:py-0 md:px-0 md:bg-transparent md:border-0 bg-theme-secondary-100 dark:md:bg-transparent dark:bg-theme-dark-950">
                    {{ $title }}
                </div>
            @endif

            <div @class([
                'flex flex-col px-4 text-sm md:py-4 md:px-6 md:space-y-3 md:text-base md:leading-5',
                'md-lg:w-[476px]' => ! $fullWidth,
            ])>
                @if ($title)
                    <div class="hidden md:inline-flex">
                        <x-general.badge class="md:py-1 md:px-2 md:text-sm">
                            {{ $title }}
                        </x-general.badge>
                    </div>
                @endif

                <div class="flex flex-col flex-1 space-y-3 whitespace-nowrap divide-y divide-dashed md:divide-none divide-theme-secondary-300 dark:divide-theme-dark-700">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
