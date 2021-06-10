@props([
    'xData' => '{}',
    'onSelected' => null,
    'defaultSelected' => '',
    'noData' => false,
])

<div
    {{ $attributes->merge(['class' => 'items-center justify-between w-full flex bg-theme-secondary-100 rounded-xl dark:bg-black relative z-10' ])}}
    @unless($noData)
        x-data="Tabs(
            '{{ $defaultSelected }}',
            {{ $xData }}
            @if($onSelected)
            , {{ $onSelected }}
            @endif
        )"
    @endunless
>
    <div role="tablist" class="flex">
        {{ $slot }}
    </div>

    <div>
        @isset($right)
            {{ $right }}
        @endisset
    </div>
</div>
