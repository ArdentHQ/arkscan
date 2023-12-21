@props([
    'innerClass' => null,
])

<div {{ $attributes }}>
    <span @class([
        'inline-flex items-center py-1 px-2 space-x-2 text-xs font-semibold rounded dark:text-white text-theme-primary-900 bg-theme-primary-100 dark:bg-theme-dark-blue-800 dim:bg-theme-dark-500',
        $innerClass,
    ])>
        <x-ark-icon name="circle.info" size="sm" class="shrink-0" />

        <span>
            @lang('pages.compatible-wallets.arkvault.disclaimer')
        </span>
    </span>
</div>
