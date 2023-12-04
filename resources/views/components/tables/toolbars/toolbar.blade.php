@props([
    'resultCount',
    'resultSuffix' => null,
    'breakpoint' => 'sm',
])

@php
    $breakpointClass = [
        false => 'flex-row justify-between items-center space-y-0',
        'sm' => 'sm:flex-row sm:justify-between sm:items-center sm:space-y-0',
        'md' => 'md:flex-row md:justify-between md:items-center md:space-y-0',
    ][$breakpoint] ?? 'sm:flex-row sm:justify-between sm:items-center sm:space-y-0';

    if ($breakpoint !== false) {
        $breakpointClass = Arr::toCssClasses([
            $breakpointClass,
            'flex-col space-y-3',
        ]);
    }
@endphp

<div @class([
    "flex pt-1 md:px-6 md:rounded-t-xl md:border md:border-b-0 md:border-theme-secondary-300 md:dark:border-theme-dark-700",
    "pb-4 md:pt-4" => $slot->isNotEmpty(),
    "pb-5 md:pt-5" => !$slot->isNotEmpty(),
    $breakpointClass,
])>
    <div class="font-semibold dark:text-theme-dark-200">
        <span class="hidden sm:inline">
            @lang('pagination.showing_x_results', ['count' => number_format($resultCount, 0)])
        </span>

        <span class="sm:hidden">
            @lang('pagination.x_results', ['count' => number_format($resultCount, 0)])
        </span>

        @if ($resultSuffix)
            <span>{{ $resultSuffix }}</span>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div>
            {{ $slot }}
        </div>
    @endif
</div>
