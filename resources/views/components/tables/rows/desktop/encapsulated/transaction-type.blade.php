@props(['model'])

<div class="text-xs font-semibold rounded border border-transparent dark:bg-transparent px-[3px] py-[2px] bg-theme-secondary-200 leading-[15px] dark:border-theme-secondary-800 dark:text-theme-secondary-500">
    <x-general.encapsulated.transaction-type :transaction="$model" />
</div>
