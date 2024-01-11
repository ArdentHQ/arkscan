@props(['model'])

<span {{ $attributes->class('text-sm leading-4.25 dark:text-theme-dark-50') }}>
    <x-percentage>{{ $model->balancePercentage() }}</x-percentage>
</span>
