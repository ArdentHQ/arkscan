@php ($isDisabled = app()->isDownForMaintenance())

<button
    wire:click="toggle"
    @class([
        'flex justify-center items-center w-8 h-8 rounded border border-theme-secondary-300 dark:border-theme-dark-800 transition-default flex-shrink-0',
        'bg-theme-secondary-200 dark:bg-theme-dark-800 cursor-not-allowed text-theme-secondary-500 dark:text-theme-dark-700' => $isDisabled,
        'text-theme-secondary-700 dark:text-theme-dark-200 dark:hover:bg-theme-dark-700 hover:bg-theme-secondary-200 hover:text-theme-secondary-900 dark:hover:text-theme-dark-50' => ! $isDisabled,
    ])"
    @if ($isDisabled)
        disabled
    @endif
>
    <x-ark-icon
        :name="$this->icon()"
        size="sm"
    />
</button>
