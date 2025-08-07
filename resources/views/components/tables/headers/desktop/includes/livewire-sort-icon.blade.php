@props([
    'sortKey',
    'componentId' => '',
])

@if ($sortKey !== null)
    <div {{ $attributes->class([
        'flex flex-col transition-default text-theme-secondary-500 dark:text-theme-dark-500',
        'group-hover/header:text-theme-secondary-900 group-hover/header:dark:text-theme-dark-50',
    ]) }}>
        <x-ark-icon
            name="arrows.caret-up"
            size="w-2 h-2"
            :class="Arr::toCssClasses(['group-[.disabled]/header:text-theme-secondary-300 group-[.disabled]/header:dark:text-theme-dark-800',
                'text-theme-primary-600 dark:text-theme-dark-blue-400' => $this->getSortKey($componentId) === $sortKey && $this->getSortDirection($componentId) === SortDirection::ASC,
            ])"
        />

        <x-ark-icon
            name="arrows.caret-down"
            size="w-2 h-2"
            :class="Arr::toCssClasses(['group-[.disabled]/header:text-theme-secondary-300 group-[.disabled]/header:dark:text-theme-dark-800',
                'text-theme-primary-600 dark:text-theme-dark-blue-400' => $this->getSortKey($componentId) === $sortKey && $this->getSortDirection($componentId) === SortDirection::DESC,
                'text-theme-secondary-500 dark:text-theme-dark-500' => $this->getSortKey($componentId) !== $sortKey,
            ])"
        />
    </div>
@endif
