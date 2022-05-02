@props ([
    'url',
    'label',
    'mobile-label',
    'active' => false,
])

<a href="{{ $url }}" {{ $attributes->class([
    'w-1/2 font-semibold text-theme-secondary-700 dark:text-theme-secondary-200 rounded-xl border-2 text-center py-3',
    'border-theme-success-600 bg-theme-success-50 dark:bg-theme-success-900' => $active,
    'border-theme-success-100 dark:border-theme-secondary-800 hover:border-theme-success-600 hover:bg-theme-success-50 dark:hover:bg-theme-success-900 bg-white dark:bg-theme-secondary-900 transition' => ! $active,
]) }}>
    <span class="hidden sm:inline leading-0">{{ $label }}</span>
    <span class="inline sm:hidden leading-0">{{ $mobileLabel }}</span>
</a>
