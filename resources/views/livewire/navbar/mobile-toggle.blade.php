@php($isActive = $this->isActive())

<button
    wire:click="toggle"
    class="relative h-8 rounded bg-theme-secondary-300 w-15 dark:bg-theme-secondary-900"
>
    <div @class([
        'absolute top-1 bg-white dark:bg-theme-secondary-800 rounded w-6 h-6 transition-default',
        'right-1' => $isActive,
        'left-1' => ! $isActive,
    ])></div>

    <div @class([
        'p-2 absolute left-0 top-0',
        'text-theme-secondary-900 dark:text-theme-secondary-200' => ! $isActive,
    ])>
        <x-ark-icon
            :name="$this->inactiveIcon"
            size="sm"
        />
    </div>

    <div @class([
        'p-2 absolute right-0 top-0',
        'text-theme-secondary-900 dark:text-theme-secondary-200' => $isActive,
    ])>
        <x-ark-icon
            :name="$this->activeIcon"
            size="sm"
        />
    </div>
</button>
