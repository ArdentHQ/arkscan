<div class="entity-header-item">
    <div class="flex items-center">
        <div class="circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
            @if ($icon ?? false)
                <x-icon :name="$icon" />
            @elseif ($avatar ?? false)
                <x-general.avatar-small :identifier="$avatar" />
            @endif
        </div>
    </div>

    <div class="flex flex-col justify-between flex-1 ml-4 font-semibold">
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
