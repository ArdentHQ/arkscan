<div class="entity-header-item">
    <div class="flex items-center">
        <div class="circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
            @if ($icon ?? false)
                @if ($iconSize ?? false)
                    <x-ark-icon :name="$icon" :size="$iconSize" />
                @else
                    <x-ark-icon :name="$icon" />
                @endif
            @elseif ($avatar ?? false)
                <x-general.avatar-small :identifier="$avatar" />
            @endif
        </div>
    </div>

    <div class="flex flex-col flex-1 justify-between ml-4 font-semibold">
        <div class="text-sm leading-tight text-theme-secondary-600 dark:text-theme-secondary-700">{{ $title }}</div>

        @if ($url ?? false)
            <a href="{{ $url }}" class="flex leading-tight link">
                <span class="truncate">{{ $text }}</span>
            </a>
        @else
            <span class="leading-tight truncate text-theme-secondary-900 dark:text-theme-secondary-200">{{ $text }}</span>
        @endif
    </div>
</div>
