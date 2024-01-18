@props([
    'width' => 'w-[70px]',
    'height' => 'h-[17px]',
    'outerClass' => null,
])

<div @class($outerClass)>
    <div {{ $attributes->class([
        'rounded-sm-md animate-pulse bg-theme-secondary-300 dark:bg-theme-dark-800',
        $width,
        $height,
    ]) }}></div>
</div>
