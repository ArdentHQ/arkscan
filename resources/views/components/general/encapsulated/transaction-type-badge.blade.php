@props(['transaction'])

<div {{ $attributes->class('text-xs font-semibold rounded border border-transparent dark:bg-transparent encapsulated-transaction-type px-[3px] py-[2px] bg-theme-secondary-200 leading-3.75 dark:border-theme-secondary-800 dark:text-theme-secondary-500') }}>
    <x-general.encapsulated.transaction-type :transaction="$transaction" />
</div>
