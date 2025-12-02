@props([
    'name',
    'first' => false,
])

<button
    type="button"
    class="flex relative items-center cursor-pointer transition-default text-theme-secondary-700 dark:text-theme-dark-200 hover:text-theme-secondary-900 dark:hover:text-theme-secondary-200 group/tab"
    @click="select('{{ $name }}')"
    @keydown.enter="select('{{ $name }}')"
    @keydown.space.prevent="select('{{ $name }}')"
    role="tab"
    id="tab-{{ $name }}"
    aria-controls="panel-{{ $name }}"
    wire:key="tab-{{ $name }}"
    @keydown.arrow-left="selectPrevTab"
    @keydown.arrow-right="selectNextTab"
    :tabindex="selected === '{{ $name }}' ? 0 : -1"
    :aria-selected="selected === '{{ $name }}'"
    {{ $attributes }}
>
    <span
        class="block py-1.5 px-3 w-full h-full font-semibold whitespace-nowrap rounded sm:rounded-lg transition-default"
        :class="{
            'dark:text-theme-dark-200 group-hover/tab:text-theme-secondary-900 dark:group-hover/tab:text-theme-dark-50 group-hover/tab:bg-theme-secondary-300 dark:group-hover/tab:bg-theme-dark-900': selected !== '{{ $name }}',
            'text-theme-secondary-900 dark:text-theme-dark-50 bg-white dark:bg-theme-dark-800': selected === '{{ $name }}',
        }"
    >{{ $slot }}</span>
</button>
