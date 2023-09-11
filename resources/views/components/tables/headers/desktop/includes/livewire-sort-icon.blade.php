@props(['id'])

@if ($id !== null)
    <div {{ $attributes->class('flex flex-col transition-default text-theme-secondary-500 dark:text-theme-dark-500 group-hover/header:text-theme-secondary-900 group-hover/header:dark:text-theme-dark-50') }}>
        <x-ark-icon
            name="arrows.chevron-up-small"
            size="w-2 h-2"
            :class="Arr::toCssClasses([
                'text-theme-primary-600 dark:text-theme-dark-blue-400' => $this->sortKey === $id && $this->sortDirection === SortDirection::ASC,
            ])"
        />

        <x-ark-icon
            name="arrows.chevron-down-small"
            size="w-2 h-2"
            :class="Arr::toCssClasses([
                'text-theme-primary-600 dark:text-theme-dark-blue-400' => $this->sortKey === $id && $this->sortDirection === SortDirection::DESC,
                'text-theme-secondary-500 dark:text-theme-dark-500' => $this->sortKey !== $id
            ])"
        />
    </div>
@endif
