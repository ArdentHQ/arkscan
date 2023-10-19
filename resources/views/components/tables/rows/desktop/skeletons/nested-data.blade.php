@props([
    'responsive' => false,
    'breakpoint' => 'lg',
    'firstOn' => null,
    'lastOn' => null,
    'nestedDataBreakpoint' => null,
])

@php
    if ($nestedDataBreakpoint) {
        $fromBreakpoint = null;
        $toBreakpoint = null;
        $height = null;

        $from = $nestedDataBreakpoint;
        if (is_array($nestedDataBreakpoint)) {
            $from = Arr::get($nestedDataBreakpoint, 'from');
            $to = Arr::get($nestedDataBreakpoint, 'to');
            $height = Arr::get($nestedDataBreakpoint, 'height');
        }

        if ($from) {
            $fromBreakpoint = [
                'sm'    => 'hidden sm:block',
                'md'    => 'hidden md:block',
                'md-lg' => 'hidden md-lg:block',
                'lg'    => 'hidden lg:block',
                'xl'    => 'hidden xl:block',
            ][$from] ?? null;
        }

        if ($to) {
            $toBreakpoint = [
                'sm'    => 'sm:hidden',
                'md'    => 'md:hidden',
                'md-lg' => 'md-lg:hidden',
                'lg'    => 'lg:hidden',
                'xl'    => 'xl:hidden',
            ][$to] ?? null;
        }
    }
@endphp

<x-ark-tables.cell
    :responsive="$responsive"
    :breakpoint="$breakpoint"
    :first-on="$firstOn"
    :last-on="$lastOn"
    :attributes="$attributes"
>
    <div class="flex flex-col space-y-1 leading-4.25">
        <x-loading.text />

        @if ($nestedDataBreakpoint)
            <div @class([
                $fromBreakpoint,
                $toBreakpoint,
            ])>
                <x-loading.text :height="$height" />
            </div>
        @endif
    </div>
</x-ark-tables.cell>
