@php($isDisabled = app()->isDownForMaintenance())
@php($isActive = $this->isActive())

<button
    wire:click="toggle"
    @class([
        'relative h-8 rounded w-15 bg-theme-secondary-200',
        'dark:bg-theme-secondary-800 text-theme-secondary-500 dark:text-theme-secondary-700' => $isDisabled,
        'dark:bg-theme-secondary-900' => ! $isDisabled,
    ])"
    @if ($isDisabled)
        disabled
    @endif
>
    <div @class([
        'absolute top-1 rounded w-6 h-6 transition-default dark:bg-theme-secondary-800',
        'right-1' => $isActive,
        'left-1' => ! $isActive,
        'bg-theme-secondary-200 ' => $isDisabled,
        'bg-white' => ! $isDisabled,
    ])></div>

    <div @class([
        'p-2 absolute left-0 top-0',
        'text-theme-secondary-900 dark:text-theme-secondary-200' => ! $isDisabled && ! $isActive,
        'text-theme-secondary-500 dark:text-theme-secondary-700' => $isDisabled,
    ])>
        <x-ark-icon
            :name="$this->inactiveIcon"
            size="sm"
        />
    </div>

    <div @class([
        'p-2 absolute right-0 top-0',
        'text-theme-secondary-900 dark:text-theme-secondary-200' => ! $isDisabled && $isActive,
        'text-theme-secondary-500 dark:text-theme-secondary-700' => $isDisabled,
    ])>
        <x-ark-icon
            :name="$this->activeIcon"
            size="sm"
        />
    </div>
</button>
