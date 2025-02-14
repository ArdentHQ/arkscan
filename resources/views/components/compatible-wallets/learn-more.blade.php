@props([
    'borderClass' => '',
    'backgroundColor' => 'bg-theme-primary-50 dark:bg-theme-dark-blue-900 dim:bg-theme-dim-blue-950',
    'padding' => 'p-6 sm:p-3 mt-6 lg:px-6',
    'titleColor' => 'text-theme-secondary-900 dark:text-white',
    'subtitleColor' => 'text-theme-secondary-700 dark:text-theme-dark-blue-400 dim:text-theme-dark-blue-600',
    'iconSize' => 'w-11 h-11',
    'home' => false,
])

<x-general.learn-more
    :border-class="$borderClass"
    icon="app-wallets.arkvault"
    :title="trans('brands.arkvault')"
    :subtitle="trans('pages.compatible-wallets.arkvault.subtitle')"
    :background-color="$backgroundColor"
    :padding="$padding"
    :title-color="$titleColor"
    :subtitle-color="$subtitleColor"
    :icon-size="$iconSize"
    :arrows-class="Arr::toCssClasses(['md-lg:bg-none md-lg:dark:bg-none',
        'arkvault-arrows' => ! $home,
        'arkvault-arrows-home' => $home,
    ])"
/>
