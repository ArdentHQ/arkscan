@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'class' => '',
    'name' => '',
])


<x-ark-tables.header
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :class="$class . ' text-left'"
>
    @isset ($slot)
        <div class="inline-flex justify-end items-center space-x-2 w-full md:justify-start">
            <div>@lang($name)</div>

            {{ $slot }}
        </div>
    @else
        @lang($name)
    @endisset
</x-ark-tables.header>
