@php ($isDisabled = app()->isDownForMaintenance())

<button
    wire:click="toggle"
    @class([
        'flex justify-center items-center w-8 h-8 rounded border border-theme-secondary-300 dark:border-theme-secondary-800 transition-default',
        'bg-theme-secondary-200 dark:bg-theme-secondary-800 cursor-not-allowed text-theme-secondary-500 dark:text-theme-secondary-700' => $isDisabled,
        'text-theme-secondary-700 dark:text-theme-secondary-600 dark:hover:bg-theme-secondary-800 hover:bg-theme-secondary-200' => ! $isDisabled,
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
