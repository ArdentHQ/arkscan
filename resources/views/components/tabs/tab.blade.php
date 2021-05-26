@props([
    'name',
])

<div
    class="relative pt-4 pb-3 ml-16 whitespace-nowrap border-b-4 cursor-pointer explorer-tab transition-default hover:text-theme-secondary-900 dark:hover:text-theme-secondary-200"
    :class="{
        'border-transparent dark:text-theme-secondary-500 ': selected !== '{{ $name }}',
        'text-theme-secondary-900 border-theme-primary-600 dark:text-theme-secondary-200 font-semibold': selected === '{{ $name }}',
    }"
    @click="select('{{ $name }}')"
    wire:key="tab-{{ $name }}"
>
    {{ $slot }}
</div>

