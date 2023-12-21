@props([
    'backgroundColor' => 'bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:!bg-theme-dim-blue-950',
    'padding' => 'py-3 px-3 mt-6 md:px-6',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:!text-theme-dark-blue-600',
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
        'flex flex-1 items-center bg-no-repeat bg-right mx-auto sm:ml-0 sm:mr-2 md-lg:bg-none md-lg:dark:bg-none',

        "sm:dim:bg-[url('/images/wallets/arrows-dim.svg')] sm:dark:bg-[url('/images/wallets/arrows-dark.svg')] sm:bg-[url('/images/wallets/arrows.svg')] xl:dim:bg-[url('/images/wallets/arrows-dim.svg')] xl:dark:bg-[url('/images/wallets/arrows-dark.svg')] xl:bg-[url('/images/wallets/arrows.svg')]" => $arrowsBreakpoint === null && ! $home,
        "sm:dim:!bg-[url('/images/home/arrows-dim.svg')] sm:dark:bg-[url('/images/home/arrows-dark.svg')] sm:bg-[url('/images/home/arrows.svg')] xl:dim:!bg-[url('/images/home/arrows-dim.svg')] xl:dark:bg-[url('/images/home/arrows-dark.svg')] xl:bg-[url('/images/home/arrows.svg')]" => $arrowsBreakpoint === null && $home,

        "xl:dim:bg-[url('/images/wallets/arrows-dim.svg')] xl:dark:bg-[url('/images/wallets/arrows-dark.svg')] xl:bg-[url('/images/wallets/arrows.svg')]" => $arrowsBreakpoint === 'xl' && ! $home,
        "xl:dim:!bg-[url('/images/home/arrows-dim.svg')] xl:dark:bg-[url('/images/home/arrows-dark.svg')] xl:bg-[url('/images/home/arrows.svg')]" => $arrowsBreakpoint === 'xl' && $home,
    ])>
        <div>
            <x-ark-icon
                name="app-wallets.arkvault"
                :size="$iconSize"
                class="dark:text-white text-theme-navy-600"
            />
        </div>

        <div class="flex flex-col justify-center ml-3 space-y-2">
            <span @class([
                'text-lg leading-5.25 font-semibold',
                $titleColor,
            ])>
                @lang('general.arkvault')
            </span>

            <span @class([
                'text-sm font-semibold leading-3.75',
                $subtitleColor,
            ])>
                @lang('pages.compatible-wallets.arkvault.subtitle')
            </span>
        </div>
    </div>

    <div class="flex items-center mt-4 sm:mt-0 sm:h-auto">
        <a
            href="@lang('pages.compatible-wallets.arkvault.url')"
            target="_blank"
            rel="noopener nofollow noreferrer"
            class="flex items-center py-3.5 w-full rounded-lg sm:mt-0 sm:w-auto md:mt-0 md:w-full lg:w-auto button-primary dim:bg-theme-dark-blue-600 sm:h-15"
        >
            <div class="flex justify-center items-center h-full text-lg leading-5.25">
                <span>
                    @lang('actions.learn_more')
                </span>
            </div>
        </a>
    </div>
</div>
