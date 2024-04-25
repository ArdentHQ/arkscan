@props([
    'backgroundColor' => 'bg-theme-success-50 dark:bg-theme-success-900 border border-transparent dark:border-theme-success-500',
    'padding' => 'p-6 mt-6 sm:py-4',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-success-700',
    'iconSize' => 'w-10 h-10',
    'buttonColor' => '!bg-theme-success-600 hover:!bg-theme-success-700',
])

@php
    $arrows = [
        "md-lg:dark:bg-[url('".Vite::image('wallets/arrows-success-dark.svg')."')] md-lg:bg-[url('".Vite::image('wallets/arrows-success.svg')."')]",
    ];
@endphp

<x-general.learn-more
    :url="trans('urls.arkconnect')"
    icon="app-wallets.arkconnect"
    icon-color="text-[#058751] dark:text-theme-success-600"
    :title="trans('pages.compatible-wallets.arkconnect.title')"
    :title-extra="trans('pages.compatible-wallets.arkconnect.title_extra')"
    :subtitle="trans('pages.compatible-wallets.arkconnect.subtitle')"
    :background-color="$backgroundColor"
    :padding="$padding"
    :title-color="$titleColor"
    :subtitle-color="$subtitleColor"
    :icon-size="$iconSize"
    :button-color="$buttonColor"
    :arrows-class="$arrows"
    mobile-tall
/>
