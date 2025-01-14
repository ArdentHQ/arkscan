@props([
    'alpineData' => '{}',
    'onSelected' => null,
    'defaultSelected' => '',
    'noData' => false,
    'right' => null,
])

<div
    {{ $attributes->merge(['class' => 'flex items-center justify-between w-faull bg-theme-secondary-100 rounded-xl dark:bg-black relative z-10' ])}}
    @unless($noData)
        x-data="Tabs(
            '{{ $defaultSelected }}',
            {{ $alpineData }}
            @if($onSelected)
            , {{ $onSelected }}
            @endif
        )"
    @endunless
>
    {{ $slot }}

    @if($right)
        <div>
            {{ $right }}
        </div>
    @endif
</div>
