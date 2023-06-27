<button
    wire:click="toggle"
    wire:loading.attr="disabled"
    class="flex justify-center items-center w-8 h-8 rounded border text-theme-secondary-700 border-theme-secondary-300 transition-default dark:text-theme-secondary-600 dark:border-theme-secondary-800 dark:hover:bg-theme-secondary-800 hover:bg-theme-secondary-200"
>
    <x-ark-icon
        :name="$this->icon()"
        size="sm"
    />
</button>
