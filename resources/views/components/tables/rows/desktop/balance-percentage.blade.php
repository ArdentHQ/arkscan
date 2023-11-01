@props(['model'])

<span {{ $attributes->class('text-sm leading-4.25') }}>
    <x-percentage>{{ $model->balancePercentage() }}</x-percentage>
</span>
