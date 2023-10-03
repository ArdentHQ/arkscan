@props([
    'width' => 'w-[70px]',
])

<div>
    <div {{ $attributes->class([
        'h-4 rounded-sm-md animate-pulse bg-theme-secondary-300 dark:bg-theme-secondary-800',
        $width,
    ]) }}></div>
</div>
