@props([
    'title',
    'text',
    'wrapperClass' => '',
    'contentClass' => 'md:ml-4 md:pr-4',
    'url' => false,
    'withoutIcon' => false,
    'icon' => false,
    'iconSize' => false,
    'avatar' => false,
    'truncate' => true,
])

<div class="entity-header-item {{ $wrapperClass }}{{ $truncate ? ' overflow-x-auto truncate' : '' }}">
    @unless($withoutIcon)
        <div class="hidden items-center md:flex">
            <div class="circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                @if ($icon)
                    @if ($iconSize)
                        <x-ark-icon :name="$icon" :size="$iconSize" />
                    @else
                        <x-ark-icon :name="$icon" />
                    @endif
                @elseif ($avatar)
                    <x-general.avatar-small :identifier="$avatar" />
                @endif
            </div>
        </div>
    @endunless

    <div class="flex flex-col flex-1 justify-between font-semibold space-y-2 p-1 -m-1 {{ $contentClass }}{{ $truncate ? ' overflow-x-auto truncate' : '' }}">
        <div class="text-sm leading-tight text-theme-secondary-500 dark:text-theme-secondary-700{{ $truncate ? ' truncate' : '' }}">{{ $title }}</div>

        @if ($url)
            <a href="{{ $url }}" class="flex leading-tight link">
                <span class="{{ $truncate ? 'truncate' : 'whitespace-nowrap' }}">{{ $text }}</span>
            </a>
        @else
            <span class="leading-tight text-theme-secondary-900 dark:text-theme-secondary-200{{ $truncate ? ' truncate' : ' whitespace-nowrap' }} ">{{ $text }}</span>
        @endif
    </div>
</div>
