@props([
    'backgroundColor' => 'bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:bg-theme-dim-blue-950',
    'padding' => 'py-3 px-3 mt-6 md:px-6',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600',
    'iconSize' => 'w-11 h-11',
    'home' => false,
    'arrowsBreakpoint' => null,
])

@php
    $arrows = [
        'md-lg:bg-none md-lg:dark:bg-none',

        "sm:dim:bg-[url('/images/wallets/arrows-dim.svg')] sm:dark:bg-[url('/images/wallets/arrows-dark.svg')] sm:bg-[url('/images/wallets/arrows.svg')] xl:dim:bg-[url('/images/wallets/arrows-dim.svg')] xl:dark:bg-[url('/images/wallets/arrows-dark.svg')] xl:bg-[url('/images/wallets/arrows.svg')]" => $arrowsBreakpoint === null && ! $home,
        "sm:dim:bg-[url('/images/home/arrows-dim.svg')] sm:dark:bg-[url('/images/home/arrows-dark.svg')] sm:bg-[url('/images/home/arrows.svg')] xl:dim:bg-[url('/images/home/arrows-dim.svg')] xl:dark:bg-[url('/images/home/arrows-dark.svg')] xl:bg-[url('/images/home/arrows.svg')]" => $arrowsBreakpoint === null && $home,

        "xl:dim:bg-[url('/images/wallets/arrows-dim.svg')] xl:dark:bg-[url('/images/wallets/arrows-dark.svg')] xl:bg-[url('/images/wallets/arrows.svg')]" => $arrowsBreakpoint === 'xl' && ! $home,
        "xl:dim:bg-[url('/images/home/arrows-dim.svg')] xl:dark:bg-[url('/images/home/arrows-dark.svg')] xl:bg-[url('/images/home/arrows.svg')]" => $arrowsBreakpoint === 'xl' && $home,
    ];
@endphp

<x-general.learn-more
    icon="app-wallets.arkvault"
    :title="trans('general.arkvault')"
    :subtitle="trans('pages.compatible-wallets.arkvault.subtitle')"
    :background-color="$backgroundColor"
    :padding="$padding"
    :title-color="$titleColor"
    :subtitle-color="$subtitleColor"
    :icon-size="$iconSize"
    :arrows-breakpoint="$arrowsBreakpoint"
    :arrows-class="$arrows"
/>
