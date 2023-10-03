@props([
    'backgroundColor' => 'bg-theme-primary-50 dark:bg-theme-dark-blue-900',
    'padding' => 'py-3 px-3 mt-6 md:pl-6',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-dark-blue-400',
    'iconSize' => 'w-10 h-10',
    'home' => false,
    'arrowsBreakpoint' => null,
])

<div {{ $attributes->class([
    'flex flex-col justify-between rounded-xl sm:flex-row',
    $padding,
    $backgroundColor,
]) }}>
    <div @class([
        'flex flex-1 items-center py-1 bg-no-repeat bg-right md-lg:bg-none mr-2',
        "sm:dark:bg-[url('/images/wallets/arrows-dark.svg')] sm:bg-[url('/images/wallets/arrows.svg')] lg:dark:bg-[url('/images/wallets/arrows-dark.svg')] lg:bg-[url('/images/wallets/arrows.svg')]" => $arrowsBreakpoint === null && ! $home,
        "xl:dark:bg-[url('/images/wallets/arrows-dark.svg')] xl:bg-[url('/images/wallets/arrows.svg')]" => $arrowsBreakpoint === 'xl' && ! $home,
        "sm:dark:bg-[url('/images/home/arrows-dark.svg')] sm:bg-[url('/images/home/arrows.svg')] lg:dark:bg-[url('/images/home/arrows-dark.svg')] lg:bg-[url('/images/home/arrows.svg')]" => $arrowsBreakpoint === null && $home,
        "xl:dark:bg-[url('/images/home/arrows-dark.svg')] xl:bg-[url('/images/home/arrows.svg')]" => $arrowsBreakpoint === 'xl' && $home,
    ])>
        <div class="px-3 sm:px-0">
            <x-ark-icon
                name="app-wallets.arkvault"
                :size="$iconSize"
                class="dark:text-white text-theme-navy-600"
            />
        </div>

        <div class="flex flex-col justify-center ml-3 space-y-2 h-[58px]">
            <span @class([
                'text-lg leading-5.25 font-semibold',
                $titleColor,
            ])>
                @lang('general.arkvault')
            </span>

            <span @class([
                'text-sm font-semibold leading-',
                $subtitleColor,
            ])>
                @lang('pages.compatible-wallets.arkvault.subtitle')
            </span>
        </div>
    </div>

    <div class="flex items-center mt-4 sm:mt-0 sm:h-auto h-15">
        <a
            href="@lang('pages.compatible-wallets.arkvault.url')"
            target="_blank"
            rel="noopener nofollow noreferrer"
            class="flex items-center w-full h-full rounded-lg sm:mt-0 sm:w-auto md:mt-0 md:w-full lg:w-auto button-primary"
        >
            <div class="flex justify-center items-center h-full">
                <span>
                    @lang('actions.learn_more')
                </span>
            </div>
        </a>
    </div>
</div>
