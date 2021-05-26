@props([
    'onSelected' => null,
    'defaultSelected' => ''
])

<div
    {{ $attributes->merge(['class' => 'items-center justify-between w-full flex bg-theme-secondary-100 rounded-xl dark:bg-black relative z-20' ])}}
    x-data="{
        selected: '{{ $defaultSelected }}',
        select(name) {
            this.selected = name;

            this.onSelected(name);
        },
        @if($onSelected)
            onSelected: {{ $onSelected }},
        @else
            onSelected: () => {},
        @endif
    }"
>
    <div class="flex">
        {{ $slot }}
    </div>

    <div>
        @isset($right)
            {{ $right }}
        @endisset
    </div>
</div>
