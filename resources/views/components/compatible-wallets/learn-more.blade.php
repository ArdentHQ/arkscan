@props([
    'backgroundColor' => 'bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:bg-theme-dim-blue-950',
    'padding' => 'p-6 sm:p-3 mt-6 lg:px-6',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600',
    'iconSize' => 'w-11 h-11',
    'home' => false,
])

@php
    $arrows = [
        'md-lg:bg-none md-lg:dark:bg-none',

        "xl:dim:bg-[url('/images/wallets/arrows-dim.svg')] xl:dark:bg-[url('/images/wallets/arrows-dark.svg')] xl:bg-[url('/images/wallets/arrows.svg')]" => ! $home,
        "xl:dim:bg-[url('/images/home/arrows-dim.svg')] xl:dark:bg-[url('/images/home/arrows-dark.svg')] xl:bg-[url('/images/home/arrows.svg')]" => $home,
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
    :arrows-class="$arrows"
/>
