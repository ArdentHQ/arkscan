@props([
    'withoutBorder'       => false,
    'icon'                => null,
    'tooltip'             => null,
    'url'                 => null,
    'wrapperClass'        => 'sm:mr-7 lg:mr-0 lg:pr-7',
    'textAlignment'       => null,
    'title',
    'text',
])

<div class="flex @if (! $withoutBorder) sm:border-r sm:border-theme-secondary-300 dark:border-theme-secondary-800 @endif {{ $wrapperClass }}">
    @if ($icon)
        {{ $icon }}
    @endif

    <div class="flex flex-col justify-center space-y-1 font-semibold truncate">
        <div class="flex items-center">
            <div class="text-sm text-theme-secondary-500 dark:text-theme-secondary-700">{{ $title }}</div>

            @if($tooltip)
                <x-ark-info :tooltip="$tooltip" class="p-1.5 ml-2" type="info" />
            @endif
        </div>

        @if ($url)
            <a href="{{ $url }}" class="flex link">
                <span class="w-full truncate {{ $textAlignment }}">{{ $text }}</span>
            </a>
        @else
            <span class="truncate text-theme-secondary-900 dark:text-theme-secondary-200 {{ $textAlignment }}">{{ $text }}</span>
        @endif
    </div>
</div>
