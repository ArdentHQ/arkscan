@props([
    'xData' => '{}',
    'onSelected' => null,
    'defaultSelected' => '',
])

<div
    {{ $attributes->class([
        'items-center justify-between inline-flex bg-theme-secondary-100 rounded-xl dark:bg-black relative z-10',
    ])}}
    x-data="Tabs(
        '{{ $defaultSelected }}',
        {{ $xData }}
        @if($onSelected)
        , {{ $onSelected }}
        @endif
    )"
>
    <div
        role="tablist"
        class="flex"
    >
        {{ $slot }}
    </div>
</div>
