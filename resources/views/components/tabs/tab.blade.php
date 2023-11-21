@props([
    'name',
])

<div {{ $attributes->only('class') }}>
    <button
        type="button"
        class="relative px-2 cursor-pointer transition-default dark:hover:text-theme-secondary-200 hover:text-theme-secondary-900"
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
        {{ $attributes->except('class') }}
    >
        <span
            class="block pt-3 pb-2.5 w-full h-full text-sm font-semibold whitespace-nowrap border-b-2 leading-4.25"
            :class="{
                'border-transparent dark:text-theme-secondary-500 ': selected !== '{{ $name }}',
                'text-theme-secondary-900 border-theme-primary-600 dark:text-theme-secondary-200': selected === '{{ $name }}',
            }"
        >{{ $slot }}</span>
    </button>
</div>
